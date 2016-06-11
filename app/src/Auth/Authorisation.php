<?php

namespace TrkLife\Auth;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Yaml\Yaml;
use TrkLife\Config;

/**
 * Class Authorisation
 *
 * @package TrkLife\Auth
 * @author George Webb <george@webb.uno>
 */
class Authorisation
{
    /**
     * @var ContainerInterface
     */
    private $c;

    /**
     * An array of roles, each with a list of their permissions
     *
     * @var array
     */
    private $roles;

    /**
     * Authorisation constructor.
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
        $this->roles = Yaml::parse(file_get_contents(Config::get('AppDir') . '/config/roles.yml'));
    }

    /**
     * Authorises a request, ensuring that the user's role is sufficient
     *
     * @param ServerRequestInterface $request
     * @return bool | ServerRequestInterface    False on failure, request on success
     */
    public function authorise(ServerRequestInterface $request)
    {
        $user = $request->getAttribute('user');

        // Check role is set for user
        if (empty($user->get('role'))) {
            return false;
        }

        // Get permission required from the request attributes
        $route = $request->getAttribute('route');
        $permission = $route->getCallable() . ':' . $request->getMethod();

        // Check user's role has permission
        if (!static::roleHasPermission($user->get('role'), $permission)) {
            return false;
        }

        return $request;
    }

    /**
     * Whether or not the given role has the given permission
     *
     * @param string $role          The name of the role
     * @param string $permission    The name of the permission
     * @return bool                 Whether or not the role has the permission
     */
    private function roleHasPermission($role, $permission)
    {
        // Check role exists
        if (!array_key_exists($role, $this->roles)) {
            return false;
        }

        // Check role has permission
        return in_array($permission, $this->roles[$role]);
    }
}
