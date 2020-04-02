<?php
namespace App\Controller\Admin;

use App\Service\OrderItemService;
use App\Service\PersonalAccountService;
use App\Service\SupplierService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderItemAdminController extends CRUDController
{
    /**
     * @var OrderItemService
     */
    private $orderItemService;
    /**
     * @var PersonalAccountService
     */
    private $personalAccountService;
    /**
     * @var SupplierService
     */
    private $supplierService;

    public function __construct(OrderItemService $orderItemService,
                                PersonalAccountService $personalAccountService,
                                SupplierService $supplierService)
    {

        $this->orderItemService = $orderItemService;
        $this->personalAccountService = $personalAccountService;
        $this->supplierService = $supplierService;
    }

    public function splitAction(Request $request)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }
        $user = $this->getUser();
        $data = $request->request->all();
        $newQuantity = $data['value'];
        $oldQuantity = $object->getQuantity();
        if($newQuantity<=0 || $newQuantity>=$oldQuantity){
            throw new NotFoundHttpException(sprintf('Неверное значение количества'));
        }
        if (!$user) {
            throw new NotFoundHttpException(sprintf('unable to find the user'));
        }
        $result = $this->orderItemService->splitOrder($object,$newQuantity);
        /*if(is_array($result) && isset($result['newOrder'])) {
            $this->admin->create($result['newOrder']);
        }*/
        return $this->json($result);
    }

    public function changeQuantityAction(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }
        $user = $this->getUser();
        $return = false;
        if($user && $data['value']>0){
            if($data['value']!=$object->getPrice()){
                $result = $this->orderItemService->changeQuantity($object, $user, $data['value']);

                if(is_array($result) && $result['changeAccount']){
                    $this->personalAccountService->changePrice($result['order'], $result['personalAccount'],
                        $result['user'], $result['oldSum'], 'Изменение количества в заказе');
                    $return = $result;
                }
                $return = ['order'=>$result];
            }
        }
        return $this->json($return);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changePriceAction(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }
        $user = $this->getUser();
        $return = false;
        if($user && $data['value']>0){
            if($data['value']!=$object->getPrice()){
                $result = $this->orderItemService->changePrice($object, $user, $data['value']);

                if(is_array($result) && $result['changeAccount']){
                    $this->personalAccountService->changePrice($result['order'], $result['personalAccount'],
                        $result['user'], $result['oldSum'], 'Изменение цены товара в заказе');
                    $return = $result;
                }
                $return = ['order'=>$result];
            }
        }
        return $this->json($return);
    }

    public function changeStatusAction(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }
        $user = $this->getUser();
        $return = false;
        if($user && $data['value']){
            if($data['value']!=$object->getStatus()){
                $result = $this->orderItemService->changeStatus($object, $user, $data['value']);
                $return = ['order'=>$result];
            }
        }
        return $this->json($return);
    }

    public function changeSupplierAction(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }
        $user = $this->getUser();
        $return = false;
        if($user && $data['value']){
            $supplier = $this->supplierService->getById($data['value']);
            if($supplier){
                $result = $this->orderItemService->changeSupplier($object, $supplier);
                $return = ['order'=>$result];
            }
        }
        return $this->json($return);
    }


}