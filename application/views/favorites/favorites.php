<!-- Favorites -->
<div id="favorites">

    <!-- Ajax requests token -->
    <form name='view-favorites'>
        <input type='hidden' name='token[name]'  value='view-favorites'>
        <input type='hidden' name='token[value]' value='<?php token('view-favorites'); ?>'>
    </form>
    <!-- Ajax request token END -->

    <!-- Heading -->
    <div class="heading">
        <div class="wrapper">

            <!-- Legend -->
            <div class="legend">

                <div class="label">
                    Favorites
                </div>

                <div class="sublabel">
                    Items you added to favorites list.
                </div>

            </div>
            <!-- Legend END -->

        </div>
    </div>
    <!-- Heading END -->

    <!-- Controls -->
    <div class="controls">

        <!-- Pagination -->
        <div class="pagination ajax-update-pagination">
            <?php include("favorites_pagination.php"); ?>
        </div>
        <!-- Pagination END -->

        <!-- Modules list -->
        <div class="module-select">

            <div class="heading">
                View:
            </div>

            <?php
                foreach($modules as $module):
                    if($module->selected):
            ?>
                        <div class="wrapper">
                            <div class="item selected">

                                <div class="label">
                                    <?php echo $module->label; ?>
                                </div>

                                <div class="count"
                                      id="ajax-update-count">
                                    <?php echo $module->count; ?>
                                </div>

                            </div>
                        </div>
            <?php
                    else:
            ?>
                        <a href="<?php
                                        public_link("favorites/list/$module->name/page-1");
                                    ?>">
                            <div class="wrapper">
                                <div class="item">

                                    <div class="label">
                                        <?php echo $module->label; ?>
                                    </div>

                                    <div class="count">
                                        <?php echo $module->count; ?>
                                    </div>

                                </div>
                            </div>
                        </a>
            <?php
                    endif;
                endforeach;
            ?>

        </div>
        <!-- Modules list END -->

    </div>
    <!-- Controls END -->

    <!-- Content -->
    <div class="content"
          id="ajax-update-items">
        <?php include("favorites_items.php"); ?>
    </div>
    <!-- Content END -->

    <!-- Controls -->
    <div class="controls">

        <!-- Pagination -->
        <div class="pagination ajax-update-pagination">
            <?php include("favorites_pagination.php"); ?>
        </div>
        <!-- Pagination END -->

    </div>
    <!-- Controls END -->

</div>
<!-- Favorites END -->