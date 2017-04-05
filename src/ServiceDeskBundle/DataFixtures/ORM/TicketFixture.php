<?php declare(strict_types = 1);
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket;

class TicketFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $bug = new Ticket();
        $bug->setTitle('Does not work');
        $bug->setDescription('Nothing happens');
        $bug->setType('bug-report');
        $bug->setOwner('john');
        $manager->persist($bug);

        $incident = new Ticket();
        $incident->setTitle('Server down :(');
        $incident->setDescription('Cannot SSH into server');
        $incident->setType('incident');
        $incident->setOwner('john');
        $manager->persist($incident);

        $manager->flush();
        //Flush a second time for the ticket number
        $manager->flush();
    }
}
