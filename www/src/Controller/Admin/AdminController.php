<?
namespace App\Controller\Admin;

use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends CoreController
{

    public function custompageAction(Request $request)
    {
        // здесь размещается код вашей страницы
        // ...

        return $this->render('custompage.html.twig', array(
            'base_template'   => $this->getBaseTemplate(),
            'admin_pool'      => $this->container->get('sonata.admin.pool'),
            'blocks'          => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks')
        ));
    }

}