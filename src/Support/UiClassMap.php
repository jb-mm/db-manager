<?php

namespace Jb\DbManager\Support;

class UiClassMap
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function all(): array
    {
        return [
            'tailwind' => [
                'body' => 'bg-gray-100 text-gray-900 min-h-screen',
                'shell' => 'flex min-h-screen',
                'sidebar' => 'w-64 bg-white border-r border-gray-200 shrink-0',
                'sidebarTitle' => 'px-4 py-3 font-semibold text-sm uppercase tracking-wide text-gray-500 border-b',
                'sidebarLink' => 'block px-4 py-2 text-sm hover:bg-gray-50 truncate',
                'sidebarLinkActive' => 'block px-4 py-2 text-sm bg-indigo-50 text-indigo-700 font-medium truncate',
                'main' => 'flex-1 p-6 overflow-auto',
                'card' => 'bg-white rounded-lg shadow-sm border border-gray-200',
                'btn' => 'inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md',
                'btnPrimary' => 'bg-indigo-600 text-white hover:bg-indigo-700',
                'btnDanger' => 'bg-red-600 text-white hover:bg-red-700',
                'btnSecondary' => 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50',
                'input' => 'block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500',
                'table' => 'min-w-full divide-y divide-gray-200 text-sm',
                'th' => 'px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-gray-50',
                'td' => 'px-3 py-2 whitespace-nowrap border-t border-gray-100',
                'alert' => 'mb-4 rounded-md bg-green-50 text-green-800 px-4 py-2 text-sm',
                'alertError' => 'mb-4 rounded-md bg-red-50 text-red-800 px-4 py-2 text-sm',
            ],
            'bootstrap' => [
                'body' => 'bg-light min-vh-100',
                'shell' => 'd-flex min-vh-100',
                'sidebar' => 'bg-white border-end',
                'sidebarTitle' => 'px-3 py-2 fw-semibold text-muted text-uppercase small border-bottom',
                'sidebarLink' => 'd-block px-3 py-2 text-decoration-none text-dark',
                'sidebarLinkActive' => 'd-block px-3 py-2 text-decoration-none bg-primary bg-opacity-10 text-primary fw-medium',
                'main' => 'flex-grow-1 p-4 overflow-auto',
                'card' => 'card shadow-sm',
                'btn' => 'btn btn-sm',
                'btnPrimary' => 'btn-primary',
                'btnDanger' => 'btn-danger',
                'btnSecondary' => 'btn-outline-secondary',
                'input' => 'form-control form-control-sm',
                'table' => 'table table-sm table-striped mb-0',
                'th' => '',
                'td' => '',
                'alert' => 'alert alert-success py-2 small',
                'alertError' => 'alert alert-danger py-2 small',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function for(string $ui): array
    {
        $all = self::all();

        return $all[$ui] ?? $all['tailwind'];
    }
}
