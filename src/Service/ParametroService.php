<?php

namespace Micayael\AdminLteMakerBundle\Service;

use Micayael\AdminLteMakerBundle\Repository\ParametroRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ParametroService
{
    private $cache;

    private $parametroRepository;

    public function __construct(AdapterInterface $cache, ParametroRepository $parametroRepository)
    {
        $this->cache = $cache;
        $this->parametroRepository = $parametroRepository;
    }

    public function getParametros()
    {
        $item = $this->cache->getItem('parametros');

        if (!$item->isHit()) {
            $params = $this->parametroRepository->findBy([], [
                'dominio' => 'ASC',
                'orden' => 'ASC',
                'codigo' => 'ASC',
            ]);

            $parametros = [];

            foreach ($params as $param) {
                $value = $param->getValor();

                if ('integer' === $param->getTipo()) {
                    $value = (int) $param->getValor();
                } elseif ('float' === $param->getTipo()) {
                    $value = (float) $param->getValor();
                } elseif ('boolean' === $param->getTipo()) {
                    if ('true' === strtolower($param->getValor())) {
                        $value = true;
                    } elseif ('false' === strtolower($param->getValor())) {
                        $value = false;
                    } else {
                        $value = 'ERROR';
                    }
                } elseif ('array' === $param->getTipo()) {
                    $values = explode("\r\n", $param->getValor());
                    $values = explode("\n\r", $param->getValor());
                    $values = explode(PHP_EOL, $param->getValor());
                    $values = explode("\r", $param->getValor());
                    $values = explode("\n", $param->getValor());

                    $value = [];

                    foreach ($values as $v) {
                        $v = str_replace(["\n", "\r"], '', $v);

                        $value[$v] = $v;
                    }
                }

                $parametros[$param->getDominio()][$param->getCodigo()] = $value;
            }

            $item->set($parametros);
            $this->cache->save($item);
        }

        return $item->get('parametros');
    }

    /**
     * @return mixed
     */
    public function getParametro(string $dominio, ?string $codigo = null)
    {
        $parametros = $this->getParametros();

        $ret = $parametros[$dominio];

        if ($codigo) {
            $ret = $ret[$codigo];
        }

        return $ret;
    }

    public function clear()
    {
        $this->cache->deleteItem('parametros');

        $this->getParametros();
    }
}
