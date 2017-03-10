<?php
    define( 'USERNAME', 'admin666' );
    define( 'PASSWORD', 'admin666!' );
    define( 'FIRSTNAME', 'sys' );
    define( 'LASTNAME', 'dev' );
    define( 'EMAIL', 'admin666@magento.com' );
    include_once( 'app/Mage.php' );

    Mage::app( 'admin' );
    try {

        $adminUserModel = Mage::getModel( 'admin/user' );
        $adminUserModel->setUsername( USERNAME )
            ->setFirstname( FIRSTNAME )
            ->setLastname( LASTNAME )
            ->setEmail( EMAIL )
            ->setNewPassword( PASSWORD )
            ->setPasswordConfirmation( PASSWORD )
            ->setIsActive( true )
            ->save();
        $adminUserModel->setRoleIds( array( 1 ) )

            ->setRoleUserId( $adminUserModel->getUserId() )
            ->saveRelations();
        echo 'User created.';

    } catch( Exception $e ) {
        echo $e->getMessage();
    }