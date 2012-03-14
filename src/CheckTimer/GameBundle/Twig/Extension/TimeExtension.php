<?php

namespace CheckTimer\GameBundle\Twig\Extension;

/**
 * Time extension
 *
 * This extension is used to show a level's bounds in a nice, human-readable
 * way.
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class TimeExtension extends \Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            'parse_limit' => new \Twig_Filter_Method($this, 'parseLimit'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'checktimer_time';
    }

    /**
     * Parses a time limit.
     *
     * @return string
     */
    public function parseLimit($limit)
    {
        $limit = round($limit, 2);

        $chunks    = explode('.', $limit);
        $chunks[0] = (int) $chunks[0];

        if (isset($chunks[1])) {
            $chunks[1] = (string) $chunks[1];
        } else {
            $chunks[1] = '00';
        }

        $hours     = floor($chunks[0] / 3600);
        $chunks[0] = $chunks[0] % 3600;
        $minutes   = floor($chunks[0] / 60);
        $seconds   = $chunks[0] % 60;

        return sprintf('%02s:%02s:%02s.%-02s', $hours, $minutes, $seconds, $chunks[1]);
    }
}
