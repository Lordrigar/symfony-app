<?php
namespace App\Tests\Unit\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Mockery;
use App\Controller\Guest\GuestController;
use App\Repository\GuestsRepository;
use App\Entity\Guests;

class GuestControllerTest extends WebTestCase
{
    /**
     * Booting Kernel and setting container is necessary in order to use container special serivces like serializer.
     * Since all controller responses are serialized, unit testing on controllers will not work without it
     */
    protected function setUp()
    {
        static::bootKernel();
    }

    public function testController()
    {
        $guest = new Guests();
        $guest->setName('test Name');
        $guestControllerMock = Mockery::mock(GuestController::class)->makePartial();
        $guestControllerMock->setContainer(self::$container);
        $guestRepositoryMock = Mockery::mock(GuestsRepository::class)->makePartial();
        $guestRepositoryMock->shouldReceive('findAll')->andReturn([$guest]);

        $result = $guestControllerMock->index($guestRepositoryMock);

        $decodedResult = json_decode($result->getContent());

        $this->assertEquals('test Name', $decodedResult[0]->name);
    }
}