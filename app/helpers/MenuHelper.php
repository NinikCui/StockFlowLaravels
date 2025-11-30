<?php

if (! function_exists('menuItems')) {

    function menuItems()
    {
        $role = session('role');
        if (! $role) {
            return [];
        }

        $scope = $role['branch'] ? 'BRANCH' : 'COMPANY';
        $permissions = $role['permissions'] ?? [];

        $menu = config("menu.$scope");
        $filtered = [];

        foreach ($menu as $item) {

            // ALWAYS SHOW
            if (isset($item['always_show']) && $item['always_show'] === true) {
                if (isset($item['href'])) {
                    $item['href'] = ltrim($item['href'], '/');
                }
                $filtered[] = $item;

                continue;
            }

            // MENU WITH CHILDREN
            if (isset($item['children'])) {
                $children = [];

                foreach ($item['children'] as $child) {
                    if (
                        ! isset($child['permission']) ||
                        in_array($child['permission'], $permissions)
                    ) {
                        $child['href'] = ltrim($child['href'], '/');
                        $children[] = $child;
                    }
                }

                if (count($children) > 0) {
                    $item['children'] = $children;
                    $filtered[] = $item;
                }

                continue;
            }

            // MENU WITHOUT CHILDREN
            if (
                ! isset($item['permission']) ||
                in_array($item['permission'], $permissions)
            ) {
                $item['href'] = ltrim($item['href'], '/');
                $filtered[] = $item;
            }
        }

        return $filtered;
    }
}
