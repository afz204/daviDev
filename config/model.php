<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 13.10
 */
 

function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 


if(isset($session_id)){

    $adminID = ''; $adminName = ''; $adminEmail = ''; $adminJabatan = ''; $adminRole = ''; $adminRoleId = ''; $adminStatus = ''; $adminJoin  = '';

    $sql1 = "SELECT users.id, users.name, users.email, users.jabatan, users.role_id, users.status, users.created_at, levels.levels, levels.ket, roles.roles FROM users
    INNER JOIN levels ON levels.id = users.jabatan
    INNER JOIN roles ON roles.id = users.role_id

    WHERE users.id = :userID";
    $stmt = $config->runQuery($sql1);
    $stmt->execute(array(':userID' => $session_id));
    $admin = array();
    while ($row = $stmt->fetch(PDO::FETCH_LAZY))
    {
        $adm = array(
            'user_id' => $row['id'],    
            'user_name'   => $row['name'],
            'user_email'    => $row['email'],
            'user_jabatan' => $row['levels'],
            'user_role_id' => $row['role_id'],
            'user_role' => $row['roles'],
            'user_status' => $row['status'],
            'user_join' => $row['created_at']    
        );

        array_push($admin, $adm);
    }
    
    if( !isset($admin[0]['user_name']) ) $admin[0]['user_name'] = '' ;

    $cat = $config->Products('*', 'menus');
    
    $datamenu = [];
    $datasubmenu = [];
    // $subcat = "SELECT staffs.id, menus.id, menus.menu, menus.links, sub_menus.id as groupid, sub_menus.submenu, sub_menus.link, previllages.weight FROM staffs
    // INNER JOIN menus ON menus.id = staffs.id_menu
    // INNER JOIN sub_menus ON sub_menus.id_menu = menus.id
    // INNER JOIN previllages ON previllages.id_submenu = sub_menus.id
    // WHERE previllages.id_admin = :adminID GROUP BY sub_menus.submenu ORDER BY sub_menus.id ASC";
    $subcat = "select previllages.id as previllagesid, previllages.weight, menus.menu as cat, menus.links as catlink, sub_menus.submenu as subcat, sub_menus.link as subcatlink 
    from previllages
    left join sub_menus on sub_menus.id = previllages.id_submenu
    left join menus on menus.id = sub_menus.id_menu
    WHERE previllages.id_admin = :adminID and menus.menu IS NOT NULL order by menus.id asc";
    $subcat = $config->runQuery($subcat);
    $subcat->execute(array(':adminID' => $session_id));
    while($col = $subcat->fetch(PDO::FETCH_LAZY)) {
        $datamenu[$col['subcatlink']] = $col['weight'];
    }
    
    
    $adminName = $admin[0]['user_name'];
    // $sql2 = "SELECT staffs.id, menus.id, menus.menu, menus.links, sub_menus.submenu, sub_menus.link, previllages.weight FROM staffs
    // INNER JOIN menus ON menus.id = staffs.id_menu
    // INNER JOIN sub_menus ON sub_menus.id_menu = menus.id
    // INNER JOIN previllages ON previllages.id_submenu = sub_menus.id
    // WHERE previllages.id_admin = :adminID GROUP BY sub_menus.submenu DESC ORDER BY menus.id";
    $sql2 = "select previllages.id as previllagesid, previllages.weight, menus.menu as cat, menus.links as catlink, sub_menus.submenu as subcat, sub_menus.link as subcatlink 
    from previllages
    left join sub_menus on sub_menus.id = previllages.id_submenu
    left join menus on menus.id = sub_menus.id_menu
    WHERE previllages.id_admin = :adminID and menus.menu IS NOT NULL order by menus.id asc";

    $stmt2 = $config->runQuery($sql2);
    $stmt2->execute(array(':adminID' => $session_id));

    $category = array();
    while ($row = $stmt2->fetch(PDO::FETCH_LAZY))
    {
        $category[] = array(
            'cat' => $row['cat'],    
            'cat_link'   => $row['catlink'],
            'subcat'    => $row['subcat'],
            'subcat_link' => $row['subcatlink'],
            'weight_page'    => $row['weight']
        );
    }

    

     $catt = unique_multidim_array($category,'cat');
    // echo '<pre>';
    // print_r($catt);
    // echo '</pre>';
    foreach ($catt as $b){
       if(in_array($menu, array($b['cat']))){
        foreach ($category as $cc){
            if(in_array($b['cat'], array($cc['cat']))){
                if(in_array($footer, array($cc['subcat_link']))){
                    $weight = $cc['weight_page']; 
                    
                }else{
                    $weight = '';
                }
            }   
           }
       }
        
    }
    
    $urlmenu = '';
    $weight = 0;
    if(isset($_GET['p'])) {
        $urlmenu = $_GET['p'];
        if(isset($datamenu[$urlmenu])) $weight = $datamenu[$urlmenu]; 
    }
    $access = $config->weightPages($weight);
}
