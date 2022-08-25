<div id="about">
    <!-- Heading -->
    <div class="heading">
        <div class="wrapper">

            <!-- Legend -->
            <div class="legend">

                <div class="label">
                    <?php echo $heading; ?>
                </div>

                <div class="sublabel">
                    <?php echo $subheading; ?>
                </div>

            </div>
            <!-- Legend END -->

            <?php
                if(!$is_registred):
            ?>
                    <!-- Actions -->
                    <div class="actions">

                        <div class="button"
                              onclick="form_tools.registration.show()">
                            <div class="name">
                                Register
                            </div>
                        </div>

                    </div>
                    <!-- Actions END -->
            <?php
                endif;
            ?>

        </div>
    </div>
    <!-- Heading END -->

    <!-- Block -->
    <div class="block">
        <div class="wrapper">
            <div class="heading-label">
                Share information about cars
            </div>

            <div class="heading-sublabel">
                Lets build most large car database
            </div>

            <div class="description">
                See newest cars of the largest automakers in the world.
                <span class="bold">Photos</span> module consists of photosets and information about cars,
                which are provided by official manufacturers or large car tuning companies.
                If you want to share some car photos you took at some event, please add them in <span class="bold">Spots</span>
                module. <span class="bold">Speed</span> module contains latest news about car industry.
            </div>

            <div class="welcome1-img">
            </div>
        </div>
    </div>
    <!-- Block END -->

    <!-- Block -->
    <div class="block">
        <div class="wrapper">
            <div class="heading-label">
                Share your opinion and track activity
            </div>

            <div class="heading-sublabel">
                Like, add to favorites, comment and follow other users
            </div>

            <div class="description">
                View latest activities at all your posts in <span class="bold">Activity</span> module.
                You can follow other users and see their latest activities in <span class="bold">Following</span> module.
            </div>

            <div class="welcome2-img">
            </div>
        </div>
    </div>
    <!-- Block END -->

    <?php
        if(!$is_registred):
    ?>
            <!-- Register button -->
            <div class="register-button-wrapper">

                <div class="register-button"
                        onclick="form_tools.registration.show()">
                    <div class="name">
                        Register
                    </div>
                </div>

            </div>
            <!-- Register button END -->
    <?php
        endif;
    ?>
</div>