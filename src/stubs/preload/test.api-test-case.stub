<?php

namespace Tests;

/**
 * Class TestCase.
 */
abstract class ApiTestCase extends TestCase
{
    /**
     * Login the given administrator or create the first if none supplied.
     *
     * @param bool $admin
     *
     * @return bool|mixed
     */
    protected function loginAsAdmin($admin = false)
    {
        if (! $admin) {
            $admin = $this->createAdmin();
        }

        $this->actingAs($admin, 'web');

        return $admin;
    }

}
