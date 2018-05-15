<?php

$modul = array(
    'produksi' => array(
        'belanja ops' => array(
            '0' => ''
        )
        ),
        'payment' => array(
            'kas besar' => array(
                '0' => ''
            ),
            'kurir' => array(
                '1' => 'centang lunas pembarayaran kurir'
            ), 
            'kas ops out' => array(
                '1' => 'print_out pdf tambah sum belanja'
            )
            ),
            'kurir' => array(
                'kurir' => array(
                    '1' => 'point'
                )
            )
        );
        echo '<pre>';
        print_r($modul);
        echo '</pre>';