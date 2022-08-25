<div class="item">
    <div class="left">
        Views
    </div>

    <div class="right">
        <?php echo $user->user_posts_views_count; ?>
    </div>
</div>

<div class="item">
    <div class="left">
        Comments
    </div>

    <div class="right">
        <?php echo $user->comments_count_at_user_posts; ?>
    </div>
</div>

<div class="item">
    <div class="left">
        Likes
    </div>

    <div class="right">
        <?php echo $user->likes_count_at_user_posts; ?>
    </div>
</div>

<div class="item">
    <div class="left">
        Followers
    </div>

    <div class="right">
        <?php echo $user->user_followers_count; ?>
    </div>
</div>