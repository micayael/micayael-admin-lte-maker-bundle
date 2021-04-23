<?php

namespace Micayael\AdminLteMakerBundle\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

class MakeCrud extends AbstractMaker
{
    private $doctrineHelper;

    private $formTypeRenderer;

    private $bundleConfig;

    public function __construct(DoctrineHelper $doctrineHelper, FormTypeRenderer $formTypeRenderer, array $bundleConfig)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->formTypeRenderer = $formTypeRenderer;
        $this->bundleConfig = $bundleConfig;
    }

    public static function getCommandName(): string
    {
        return 'make:app:crud';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp('')
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm-pack'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );

        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        // Entity
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityClassName = $entityClassDetails->getShortName();
        $entityClassFullName = $entityClassDetails->getFullName();

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassFullName);

        $entityMetadata = $this->doctrineHelper->getMetadata($entityClassFullName);

        $manyToOneFields = [];

        foreach ($entityMetadata->associationMappings as $fieldName => $metadata) {
            if (isset($metadata['joinColumns']) && isset($metadata['isOwningSide']) && true === $metadata['isOwningSide']) {
                $manyToOneFields[$fieldName] = $metadata;
            }
        }

        // Repository

        $repositoryVars = [];

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\'.$entityDoctrineDetails->getRepositoryClass(),
                'Repository\\',
                'Repository'
            );

            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst(Inflector::singularize($repositoryClassDetails->getShortName())),
            ];
        }

        // Form

        $formClassDetails = $generator->createClassNameDetails(
            $entityClassName.'Type',
            'Form\\',
            'Type'
        );

        //--------------------------------------------------------------------------------------------------------------
        // Preguntas
        //--------------------------------------------------------------------------------------------------------------

        $entityVarPlural = lcfirst(Inflector::pluralize($entityClassName));
        $question = new Question('$entityVarPlural', $entityVarPlural);
        $entityVarPlural = $io->askQuestion($question);

        $entityVarSingular = lcfirst($entityClassName);
        $question = new Question('$entityVarSingular', $entityVarSingular);
        $entityVarSingular = $io->askQuestion($question);

        //--------------------------------------------------------------------------------------------------------------

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $question = new Question('$entityTwigVarPlural', $entityTwigVarPlural);
        $entityTwigVarPlural = $io->askQuestion($question);

        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);
        $question = new Question('$entityTwigVarSingular', $entityTwigVarSingular);
        $entityTwigVarSingular = $io->askQuestion($question);

        //--------------------------------------------------------------------------------------------------------------
        $titlePlural = ucfirst($entityVarPlural);
        $question = new Question('$titlePlural', $titlePlural);
        $titlePlural = $io->askQuestion($question);

        $titleSingular = ucfirst($entityClassName);
        $question = new Question('$titleSingular', $titleSingular);
        $titleSingular = $io->askQuestion($question);

        //--------------------------------------------------------------------------------------------------------------

        $routeName = $entityTwigVarSingular;
        $question = new Question('$routeName', $routeName);
        $routeName = $io->askQuestion($question);

        //--------------------------------------------------------------------------------------------------------------

        $question = new ConfirmationQuestion('Cambiar url_context, templates_path, controller_namespace?', false);
        $realizarCambiosAvanzados = $io->askQuestion($question);

        $urlContext = $this->bundleConfig['url_context'];
        $templatesPath = $this->bundleConfig['template_base_path'].$entityTwigVarSingular;
        $controllerNamespace = $this->bundleConfig['controller_base_namespace'].$entityClassName.'\\';

        if ($realizarCambiosAvanzados) {
            $question = new Question('$urlContext', $urlContext);
            $urlContext = $io->askQuestion($question);

            $question = new Question('$templatesPath', $templatesPath);
            $templatesPath = $io->askQuestion($question);

            $question = new Question('$controllerNamespace', $controllerNamespace);
            $controllerNamespace = $io->askQuestion($question);

            if ('\\' !== substr($controllerNamespace, strlen($controllerNamespace) - 1, 1)) {
                $controllerNamespace .= '\\';
            }
        }

        //--------------------------------------------------------------------------------------------------------------
        // Variables
        //--------------------------------------------------------------------------------------------------------------

        $variables = array_merge($repositoryVars, [
            'entity_full_class_name' => $entityClassFullName,
            'entity_class_name' => $entityClassName,
            'entity_class_name_upper' => strtoupper($entityClassName),

            'form_full_class_name' => $formClassDetails->getFullName(),
            'form_class_name' => $formClassDetails->getShortName(),

//            'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()),
            'route_name' => $routeName,

            'templates_path' => $templatesPath,

            'entity_identifier' => $entityDoctrineDetails->getIdentifier(),

            'entity_var_plural' => $entityVarPlural,
            'entity_var_singular' => $entityVarSingular,

            'entity_twig_var_plural' => $entityTwigVarPlural,
            'entity_twig_var_singular' => $entityTwigVarSingular,

            'sql_alias' => substr($entityTwigVarSingular, 0, 1),

            'entity_display_fields' => $entityDoctrineDetails->getDisplayFields(),
            'entity_form_fields' => $entityDoctrineDetails->getFormFields(),

            'title_plural' => $titlePlural,
            'title_singular' => $titleSingular,

            'url_context' => $urlContext,
            'controller_base_namespace' => substr($controllerNamespace, 0, strlen($controllerNamespace) - 1),

            'many_to_one_fields' => $manyToOneFields,
        ]);

        //--------------------------------------------------------------------------------------------------------------
        // Form
        //--------------------------------------------------------------------------------------------------------------

        $formFields = $entityDoctrineDetails->getFormFields();

        // Para poner el campo revisión como hidden
        $formFields['revision'] = [
            'type' => HiddenType::class,
            'options_code' => null,
        ];

        if (!class_exists($formClassDetails->getFullName())) {
            $this->formTypeRenderer->render(
                $formClassDetails,
                $formFields,
                $entityClassDetails
            );
        }

        //--------------------------------------------------------------------------------------------------------------
        // Controllers
        //--------------------------------------------------------------------------------------------------------------

        $controllers = [
            'Index',
            'New',
            'Show',
            'Edit',
            'Delete',
        ];

        foreach ($controllers as $controller) {
            $controllerCapitalize = ucfirst($controller);

            $controllerClassDetails = $generator->createClassNameDetails(
                $controllerCapitalize.'Controller',
                $controllerNamespace,
                'Controller'
            );

            $generator->generateController(
                $controllerClassDetails->getFullName(),
                __DIR__.'/../Resources/skeleton/crud/controller/'.$controllerCapitalize.'Controller.tpl.php',
                array_merge($variables)
            );
        }

        //--------------------------------------------------------------------------------------------------------------
        // Templates
        //--------------------------------------------------------------------------------------------------------------

        $templates = [
            '_delete_form' => $variables,
            '_show_data' => $variables,
            'edit' => $variables,
            'index' => $variables,
            'new' => $variables,
            'show' => $variables,
            'delete' => $variables,
        ];

        foreach ($templates as $template => $variables) {
            $generator->generateTemplate(
                $templatesPath.'/'.$template.'.html.twig',
                __DIR__.'/../Resources/skeleton/crud/templates/'.$template.'.tpl.php',
                $variables
            );
        }

        //--------------------------------------------------------------------------------------------------------------
        // Routes
        //--------------------------------------------------------------------------------------------------------------

        $generator->generateFile(
            'config/routes/'.$routeName.'.yaml',
            __DIR__.'/../Resources/skeleton/crud/routes.tpl.php',
            $variables
        );

        //--------------------------------------------------------------------------------------------------------------
        // Actions View Helper
        //--------------------------------------------------------------------------------------------------------------

        $generator->generateClass(
            'App\\Twig\\ViewHelper\\Action\\'.$entityClassName.'ActionsViewHelper',
            __DIR__.'/../Resources/skeleton/crud/ActionsViewHelper.tpl.php',
            $variables
        );

        //--------------------------------------------------------------------------------------------------------------

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', $urlContext.$routeName));
    }
}
