<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CustomRolesBuilder extends EditableRolesBuilder
{

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $rolesHierarchy;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, Pool $pool, array $rolesHierarchy = [])
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
    }

    /**
     * @param string|bool|null $domain
     * @param bool             $expanded
     *
     * @return array
     */
    public function getRoles($domain = false, $expanded = true)
    {
        $roles = [];

        if (!$this->tokenStorage->getToken()) {
            return $roles;
        }


        $isMaster = $this->authorizationChecker->isGranted(
            $this->pool->getOption('role_super_admin', 'ROLE_SUPER_ADMIN')
        );
        $makeRoles = $this->authorizationChecker->isGranted('ROLE_SONATA_USER_ADMIN_USER_LIST') ||
            $this->authorizationChecker->isGranted('ROLE_ADMIN');
        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($makeRoles || $isMaster) {
                $roles[$name] = $this->translateRole($name, $domain);
                foreach ($rolesHierarchy as $role) {
                    $roles_keys[] = $role;
                    if (!isset($roles[$role])) {
                        $roles[$role] = $this->translateRole($role, $domain);
                    }
                }
            }
        }

        unset($roles['ROLE_SUPER_ADMIN']);
        //$roles = array_diff($roles, $roles_keys);
        return $roles;
    }


    /*
 * @param string $role
 * @param string|bool|null $domain
 *
 * @return string
 */
    private function translateRole($role, $domain)
    {
        $this->translator = $this->pool->getContainer()->get('translator');
        // translation domain is false, do not translate it,
        // null is fallback to message domain
        if (false === $domain || !isset($this->translator)) {
            return $role;
        }

        return $this->translator->trans($role, [], $domain);
    }


    /*
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }


    /**
     * @param string|bool|null $domain
     *
     * @return array
     */
    public function getRolesReadOnly($domain = false)
    {
        $rolesReadOnly = [];

        if (!$this->tokenStorage->getToken()) {
            return $rolesReadOnly;
        }

        $this->iterateAdminRoles(function ($role, $isMaster) use ($domain, &$rolesReadOnly): void {
            if (!$isMaster && $this->authorizationChecker->isGranted($role)) {
                // although the user has no MASTER permission, allow the currently logged in user to view the role
                $rolesReadOnly[$role] = $this->translateRole($role, $domain);
            }
        });

        return $rolesReadOnly;
    }

    private function iterateAdminRoles(callable $func): void
    {
        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();
            // TODO get the base role from the admin or security handler
            $baseRole = $securityHandler->getBaseRole($admin);

            if (0 === \strlen($baseRole)) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            foreach ($admin->getSecurityInformation() as $role => $permissions) {
                $role = sprintf($baseRole, $role);
                \call_user_func($func, $role, $isMaster, $permissions);
            }
        }
    }
}
