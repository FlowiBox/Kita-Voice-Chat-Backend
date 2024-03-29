<?php


namespace App\Traits\AdminTraits;


use Encore\Admin\Auth\Permission;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;

trait AdminControllersTrait
{

    public function create ( Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('create-'.$this->permission_name);
        }

        return parent ::create ( $content ); // TODO: Change the autogenerated stub
    }

    public function show ( $id , Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('show-'.$this->permission_name);
        }

        return parent ::show ( $id , $content ); // TODO: Change the autogenerated stub
    }

    public function edit ( $id , Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('edit-'.$this->permission_name);
        }

        return parent ::edit ( $id , $content ); // TODO: Change the autogenerated stub
    }

    public function destroy ( $id )
    {
        if (!Admin::user()->can('*')){
            Permission::check('delete-'.$this->permission_name);
        }

        return parent::destroy ($id);
    }

    public function extendGrid($grid){
        $permission_name = $this->permission_name;

        if (!Admin::user()->can('*')) {
            if ( ! Admin ::user () -> can ( 'edit-' . $permission_name ) ) {
                $grid -> hiddenColumns = $this -> hiddenColumns;
            }
            if ( ! Admin ::user () -> can ( 'delete-' . $permission_name ) ) {
                $grid -> disableRowSelector ();
            }
            if ( ! Admin ::user () -> can ( 'create-' . $permission_name ) ) {
                $grid -> disableCreateButton ();
            }
            $grid -> actions (
                function ( $actions ) use ( $permission_name ) {

                    // The roles with this permission will not able to see the delete button in actions column.
                    if ( ! Admin ::user () -> can ( 'delete-' . $permission_name ) ) {
                        $actions -> disableDelete ();
                    }
                    if ( ! Admin ::user () -> can ( 'edit-' . $permission_name ) ) {
                        $actions -> disableEdit ();
                    }
                    if ( ! Admin ::user () -> can ( 'show-' . $permission_name ) ) {
                        $actions -> disableView ();
                    }
                }
            );
        }

    }


    public function extendShow($show){
        if (!Admin::user()->can('*')) {
            $show -> panel ()
                -> tools (
                    function ( $tools ) {
                        $tools -> disableEdit ();
                        $tools -> disableList ();
                        $tools -> disableDelete ();
                    }
                );
        }
    }


}
