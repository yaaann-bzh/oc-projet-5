<?php

namespace framework;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Michelf\Markdown;

class Extension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('markdown', [$this, 'markdownParse'], ['is_safe' => ['html']]),
            new TwigFilter('showAge', [$this, 'setAge'])
        ];
    }
    
    public function getFunctions() {
        return [
            new TwigFunction('profile_pic_exists', [$this, 'profilePicExists'])
        ];
    }

    public function markdownParse($value) {
        return Markdown::defaultTransform($value);
    }

    public function setAge(\DateTime $dateTime)
    {
        $now = new \DateTime();
        $age = $now->diff($dateTime);
        if ($age->m > 0) {
            return 'Il y a plus de 1 mois.';
        }
        elseif ($age->d > 1) {
            return 'Il y a '. $age->d . ' jours.';
        }
        elseif ($age->h > 1) {
            return 'Il y a '. $age->h . ' heures.';
        }
        else {
            return 'A l\'instant.';
        }
    }
    
    public function profilePicExists($id) {
        $fileName = '../public/assets/profile_pic/profile_pict_' . $id . '.png';

        if (file_exists($fileName)) {
            return true;
        }
        return false;
    }
}