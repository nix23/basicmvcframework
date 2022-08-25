<!-- Page heading -->
<div class="page-heading">
    <div class="wrapper">

        <div class="legend">
            <div class="name">
                Users
            </div>

            <div class="subname">
                <span>
                    List
                </span>

                <span class="separator padding">
                    >>
                </span>

                <span class="padding">
                    <a href="<?php admin_link("users"); ?>">
                        All
                    </a>
                </span>
            </div>
        </div>

        <?php echo $settings; ?>

    </div>
</div>
<!-- Page heading END -->

<!-- Page controls -->
<div class="page-controls">

    <!-- Pagination -->
    <div class="pagination">
        <div class="wrapper ajax-pagination">
            <?php include("users_list_pagination.php"); ?>
        </div>
    </div>
    <!-- Pagination END -->

    <!-- Sorting -->
    <div class="sorting">
        <div class="heading">
            Sort by:
        </div>

        <?php
            foreach($sort_items as $sort_item):
        ?>
                <div class="wrapper">
                    <a href="<?php
                                    $link  = "users/list/";
                                    $link .= "page-$current_page/";
                                    $link .= "sort-$sort_item->sort/";
                                    $link .= "prefix-$current_prefix";
                                    admin_link($link);
                                ?>">
                        <div class="<?php
                                            if($sort_item->selected)
                                                echo "item selected";
                                            else
                                                echo "item active";
                                        ?>">
                            <?php echo ucfirst($sort_item->name); ?>
                        </div>
                    </a>
                </div>
        <?php
            endforeach;
        ?>
    </div>
    <!-- Sorting END -->

    <!-- Username prefixes -->
    <div class="prefixes">
        <?php
            foreach($username_prefixes as $prefix):
                if($prefix->selected):
        ?>
                    <div class="prefix selected">
                        <div class="spacer">
                        </div>

                        <div class="text">
                            <?php echo ucfirst($prefix->name); ?>
                        </div>

                        <div class="spacer">
                        </div>
                    </div>
        <?php
                else:
        ?>
                    <a href="<?php
                                    $link  = "users/list/";
                                    $link .= "page-$current_page/";
                                    $link .= "sort-$selected_sort/";
                                    $link .= "prefix-$prefix->name";
                                    admin_link($link);
                                ?>">
                        <div class="prefix active">
                            <div class="spacer">
                            </div>

                            <div class="text">
                                <?php echo ucfirst($prefix->name); ?>
                            </div>

                            <div class="spacer">
                            </div>
                        </div>
                    </a>
        <?php
                endif;
            endforeach;
        ?>
    </div>
    <!-- Username prefixes END -->

</div>
<!-- Page controls END -->

<!-- Token -->
<form name='users-list'>
    <input type='hidden' name='token[name]'  value='users-list'>
    <input type='hidden' name='token[value]' value='<?php token('users-list'); ?>'>
</form>
<!-- Token END -->

<!-- Users list -->
<div id="users"
      class="ajax-users-list-items">
    <?php include("users_list_items.php"); ?>
</div>
<!-- Users list END -->

<!-- Page controls -->
<div class="page-controls">

    <!-- Pagination -->
    <div class="pagination">
        <div class="wrapper ajax-pagination">
            <?php include("users_list_pagination.php"); ?>
        </div>
    </div>
    <!-- Pagination END -->

</div>
<!-- Page controls END -->