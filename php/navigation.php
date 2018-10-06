<nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark">
    <a class="navbar-brand" href="#"><?=$device['device'] == 'MOBILE' ? 'BD Indonesia' : 'Bunga Davi Indonesia'?></a>
    <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <?php foreach ($catt as $cat){ ?>
                <li class="nav-item <?=$menu == $cat['cat_link'] ? 'active' : '' ?>">
                    <a style="text-transform: capitalize;" class="nav-link" href="<?=URL?><?=$cat['cat_link']?>"><?=$cat['cat']?> <span class="sr-only">(current)</span></a>
                </li>
            <?php } ?>
        </ul>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="<?=URL?>logout">Sign out</a>
            </li>
        </ul>
    </div>
</nav>

<div class="nav-scroller bg-white box-shadow">
    <nav class="nav nav-underline">
        <?php
        foreach ($catt as $parent_menu){
            if(in_array($menu, array($parent_menu['cat']))){
                foreach ($category as $subcat){
                    if(in_array($subcat['cat'], array($parent_menu['cat']))) {
                    ?>
                        <a style="text-transform: capitalize;" class="nav-link <?=$footer == $subcat['subcat_link'] ? 'active' : ''?>" href="<?=URL?><?=$subcat['cat']?>/?p=<?=$subcat['subcat_link']?>">
                            <?=$subcat['subcat']?>
                        <?php if(in_array($subcat['subcat_link'], ['users'])) { ?>
                                <span class="badge badge-pill bg-success align-text-bottom" style="color: #fff;"><?=$totalUser?></span>
                        <?php } ?> 
                        <?php if(in_array($subcat['subcat_link'], ['process'])) { ?>
                                <span class="badge badge-pill bg-success align-text-bottom" style="color: #fff;"><?=$process?></span>
                        <?php } ?> 
                        <?php if(in_array($subcat['subcat_link'], ['delivery'])) { ?>
                                <span class="badge badge-pill bg-success align-text-bottom" style="color: #fff;"><?=$delivery?></span>
                        <?php } ?> 
                        <?php if(in_array($subcat['subcat_link'], ['order'])) { ?>
                                <span class="badge badge-pill bg-success align-text-bottom" style="color: #fff;"><?=$neworder?></span>
                        <?php } ?> 
                        <?php if(in_array($subcat['subcat_link'], ['report'])) { ?>
                                <span class="badge badge-pill bg-success align-text-bottom" style="color: #fff;"><?=$report?></span>
                        <?php } ?> 
                   <?php } 
                } 
            }
        } ?>
            </a>
    </nav>
</div>