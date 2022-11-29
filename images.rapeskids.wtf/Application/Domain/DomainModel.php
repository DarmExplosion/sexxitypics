<?php

namespace Application\Domain;

use Application\Domain\Exception\DomainValidationException;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class DomainModel
{
    /**
     * @var array|string[]
     */
    public array $domainList = [
        'exity.pics',
        'sexity.pics',
        'floppa.pics',
        'lability.cc',
        'floppa-is-the.best',
        'sticky.pizza',
        'cockmenu.best',
        'dogehash.live',
        'xovs.live',
        'fagcord.com',
        'kikecord.com',
        'criminals.black',
        'lixo.store',
        'popitchi.store',
        'kaarnival.club',
        'karnivall.club',
        'water-is-overrated.club',
        'send-nudes.club',
        'rohliksense.club',
        'floppas-are.cool',
        'cepo-is.gay',
        'floppa.technology',
        'exity.xyz',
        'among-us.agency',
        'hoody.world',
        'bingus-is-s.us',
        'slaup-is-femboy.club',
        'nekoplease-is-gay.xyz',
        'adolf.tech',
        'amogus-sussy-impo.store',
        'avocado-from-mexi.co',
        'camel-f.art',
        'fbi-is-watching-your.cam',
        'you-are-going-to-brazil.today',
        'hamood-habibi.cc'
    ];

    /**
     * @param array $post
     * @return bool
     * @throws DomainValidationException
     */
    public function validateDomainFromArray(array $post): bool
    {
        if(!in_array($post['domain'], $this->domainList, false))
        {
            throw new DomainValidationException('Cannot change domain.');
        }

        return true;
    }
}