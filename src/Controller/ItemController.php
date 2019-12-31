<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Service\PlaidClient;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class ItemController
 * @IsGranted("ROLE_USER")
 *
 * @package App\Controller
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/items", name="app_items_list")
     * @param PlaidClient $plaidClient
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PlaidClient $plaidClient)
    {
        $user = $this->getUser();
        /**@var $user User */
        return $this->render('item/items.html.twig', [
            'user' => $user,
            'env' => $plaidClient->getEnv(),
            'secret' => $plaidClient->getSecret(),
        ]);
    }

    /**
     * @Route("/item/link", name="app_exchange_public_token")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PlaidClient $plaidClient
     * @return JsonResponse
     */
    public function link(Request $request, EntityManagerInterface $entityManager, PlaidClient $plaidClient)
    {
        $content = (new JsonDecode())->decode($request->getContent(), JsonEncoder::FORMAT);
        $token = $plaidClient->exchangePublicToken($content->publicToken);

        $item = (new Item())
            ->setToken($token['access_token'])
            ->setName($content->metadata->institution->name)
            ->setExternalId($token['item_id'])
            ->setCustomer($this->getUser());

        $entityManager->persist($item);
        $entityManager->flush();

        return new JsonResponse(["item" => $item]);
    }

    /**
     * @Route("/item/{id}/transactions", name="app_item_transactions")
     * @ParamConverter("item", options={"mapping": {"id": "id"}})
     * @param Item $item
     */
    public function transactions(Item $item, PlaidClient $plaidClient)
    {
        dd($plaidClient->getTransactions($item));
    }

    /**
     * @Route("/item/{id}", name="app_item")
     * @ParamConverter("item", options={"mapping": {"id": "id"}})
     * @param Item $item
     */
    public function view(Item $item, PlaidClient $plaidClient)
    {
        dd($plaidClient->getItem($item));
    }
}
