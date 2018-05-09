<div id="form-submenu" class="hidden">
    <div class="row justify-content-center">
        <div class="col-4">
            <div class="card border-dark mb-3">
                <div class="card-header bg-transparent border-dark">Form Tambah Submenu <div id="titleSubmenu"></div></div>
                <div class="card-body">

                    <form id="form-submenu" method="post" data-parsley-validate="" autocomplete="off">
                        <div class="form-group">
                            <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminSub">
                            <input type="hidden" value="" id="menuID">
                            <input type="text"
                                   data-parsley-minLength="3" data-parsley-maxLength="255"
                                   class="form-control" placeholder="submenu" id="nameSub" required>
                        </div>
                        <div class="form-group">
                            <input type="text"
                                   data-parsley-minLength="3" data-parsley-maxLength="255"
                                   class="form-control" placeholder="link submenu" id="linkSub" required>
                        </div>

                        <p>
                            <button type="submit" class="btn btn-sm btn-block btn-primary">submit submenu</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="listMenu" <?=$access['read']?>>
    <div class="row">
        <?php while ($rows = $menus->fetch(PDO::FETCH_LAZY)){ ?>
            <div class="col-6 col-md-2 col-lg-2">
                <div class="card border-success mb-3 card-body">
                    <button class="btn btn-block btn-success subMenu" data-name="<?=$rows['menu']?>" data-id="<?=$rows['id']?>"><?=$rows['menu']?></button>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<hr>
<div id="detailMenu">

</div>