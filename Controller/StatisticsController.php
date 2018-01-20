<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Model\Paginator;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{
    /**
     * display the admin navigation
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function indexAction(Request $request)
    {
        // build basic form
        $builder = $this->createFormBuilder(null);
        $builder->add(
            'date',
            DateType::class,
            array(
                'input'  => 'timestamp',
                'widget' => 'choice',
                'data' => time(),
                'format' => 'yyyy-MM-dd',
            )
        );
        // get form
        $form = $builder->getForm();

        $date = new \DateTime();

        $year = $date->format('Y');
        $month = $date->format('m');

        if ($request->getMethod() == 'POST') {

             if ($form->isValid()) {
                 $dataForm = $request->request->get('form');
                 $year = $dataForm['date']['year'];
                 $month = $dataForm['date']['month'];
             }
        }


        $statisticsManager = $this->get('kitpages_shop.statisticsManager');

        /************************************
        ********** sales per month **********
        *************************************/
        $dateStart = new \DateTime("$year-01-01 00:00:00");
        $dateEnd = new \DateTime("$year-01-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 year'));

        for($i=1; $i<=12; $i++) {
            $dataStatisticList['salesPerMonth'][$i] = 0;
        }

        $querySelect = array(
            'orderPriceWithoutVatTotal' => 'orderPriceWithoutVatTotal'
        );

        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y-%m'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticSalesPerMonth = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);
        foreach($dataStatisticSalesPerMonth as $data) {
            $dataDate = new \DateTime($data['stateDate']);
            $dataStatisticList['salesPerMonth'][$dataDate->format('n')] = $data['orderPriceWithoutVatTotal'];
        }

        /************************************
        **** sales per day for one month ****
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));
        for($i=1; $i<=intval(date("t",$dateStart->getTimestamp())); $i++) {
            $dataStatisticList['salesPerDay'][$i] = 0;
        }

        $querySelect = array(
            'orderPriceWithoutVatTotal' => 'orderPriceWithoutVatTotal'
        );
        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y-%m-%d'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticSalesPerDay = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);

        foreach($dataStatisticSalesPerDay as $data) {
            $dataDate = new \DateTime($data['stateDate']);
            $dataStatisticList['salesPerDay'][$dataDate->format('j')] = $data['orderPriceWithoutVatTotal'];
        }

        /************************************
        *** top ten products of the month ***
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));
        $dataStatisticList['salesTopTen'] = array();

        $querySelect = array(
            'orderLinePriceWithoutVatTotal' => 'orderLinePriceWithoutVatTotal',
            'shopName' => 'shopName',
            'shopReferenceQuantity' => 'shopReferenceQuantity'
        );
        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'shopReference' => 'shopReference',
        );
        $queryOrderBy = array(
            'orderLinePriceWithoutVatTotal' => 'DESC'
        );

        $dataStatisticListSalesTopTen = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);

        $nbProductDisplay = 10;
        $i = 0;
        $other = array(
            'product' => 'other',
            'total price without vat' => 0,
            'quantity' => 0
        );

        foreach($dataStatisticListSalesTopTen as $data) {
            $dataStatisticList['salesTopTen'][] = array(
                'product' => $data['shopName'],
                'total price without vat' => $data['orderLinePriceWithoutVatTotal'],
                'quantity' => $data['shopReferenceQuantity']
            );
            $i++;
            if ($nbProductDisplay < $i) {
                $other['total price without vat'] = $other['total price without vat'] + $data['orderLinePriceWithoutVatTotal'];
                $other['quantity'] = $other['quantity'] + $data['shopReferenceQuantity'];
            }
        }

        $dataStatisticList['salesTopTen'] = array_slice($dataStatisticList['salesTopTen'], 0, 10);
        if ($nbProductDisplay < $i) {
            $dataStatisticList['salesTopTen'][] = $other;
        }

        /************************************
        ************* sale by category ******
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));
        $dataStatisticList['salesPerCategory'] = array();

        $querySelect = array(
            'orderLinePriceWithoutVatTotal' => 'orderLinePriceWithoutVatTotal'
        );
        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'shopCategory' => 'shopCategory'
        );
        $queryOrderBy = array();

        $dataStatisticListSalesPerCatehory = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);
        foreach($dataStatisticListSalesPerCatehory as $data) {
            $dataStatisticList['salesPerCategory'][$data['shopCategory']] = $data['orderLinePriceWithoutVatTotal'];
        }

        arsort($dataStatisticList['salesPerCategory']);





        return $this->render(
            'KitpagesShopBundle:Admin:statistic.html.twig',
            array(
                'form' => $form->createView(),
                'dataStatisticList' => $dataStatisticList
            )
        );
    }



}
