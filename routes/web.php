<?php

Route::group(['namespace' => 'Botble\Impersonate\Http\Controllers', 'middleware' => 'web'], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'impersonates'], function () {

            Route::get('impersonate/{id}', [
                'as' => 'users.impersonate',
                'uses' => 'ImpersonateController@getImpersonate',
                'permission' => ACL_ROLE_SUPER_USER,
            ]);

            Route::get('leave-impersonation', [
                'as' => 'users.leave_impersonation',
                'uses' => 'ImpersonateController@leaveImpersonation',
                'permission' => false,
            ]);

        });
    });

});
