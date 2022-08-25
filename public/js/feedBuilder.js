feedBuilder = {
    areAllFeedDataLoaded: false,
    isMostActivePostsDataLoaded: false,
    isLastPostsDataLoaded: false,
    isLinkedPostsDataLoaded: false,

    mostActivePostsData: [],
    lastPostsData: [],
    linkedPostsData: [],
    postsData: [],

    viewedItemId: 0,
    itemWidth: 270,
    itemHeight: 100,
    outsideFeedItemVerticalMargin: 15,
    outsideFeedItemHorizontalMargin: 40,

    FEED_MODES: {OUTSIDE: 0, INSIDE: 1},
    feedMode: null,
    lastFeedMode: null,

    feedItems: [],
    mostActivePostsItems: [],
    lastPostsItems: [],
    linkedPostsItems: [],

    maxMostActivePostsItemsCountPerInsideFeed: 6,
    maxLastPostsItemsCountPerInsideFeed: 6,
    maxLinkedPostsItemsCountPerInsideFeed: 6,

    outsideFeedItemsMaxCountPerOneSide: 0,
    $outsideFeedLeftSideWrapper: null,
    $outsideFeedRightSideWrapper: null,

    $insideFeedTopWrapper: null,
    $insideFeedBottomWrapper: null,

    showInsideTopFeed: false,
    showInsideBottomFeed: false,

    initialize: function()
    {
        if(php_vars.current_controller == "photos" || php_vars.current_controller == "spots"
            || php_vars.current_controller == "speed" || php_vars.current_controller == "videos")
        {
            if(php_vars.current_action == "view")
            {
                this.loadFeedData();

                var me = this;
                $(window).on("resize", function() {
                    if(me.areAllFeedDataLoaded)
                    {
                        me.renderFeed();
                        me.bindFeedEvents();
                    }
                })
            }
        }
    },

    loadFeedData: function()
    {
        this.areAllFeedDataLoaded = false;
        this.isMostActivePostsDataLoaded = false;
        this.isLastPostsDataLoaded = false;
        this.isLinkedPostsDataLoaded = false;

        this.mostActivePostsData = [];
        this.lastPostsData = [];
        this.linkedPostsData = [];

        ajax.process("feed", "getMostActivePostsData", "feedAfterMostActivePostsDataLoaded", false, "none");
        ajax.process("feed", "getLastPostsData", "feedAfterLastPostsDataLoaded", false, "none");

        this.viewedItemId = $("body").find("[data-viewed-item-id]").attr("data-viewed-item-id");
        var params = php_vars.current_controller + "/" + this.viewedItemId + "/" + "feedAfterLinkedPostsDataLoaded";
        ajax.process("feed", "getLinkedPostsData", params, false, "none");
    },

    renderFeedIfAllDataIsLoaded: function()
    {
        if(this.isMostActivePostsDataLoaded && this.isLastPostsDataLoaded && this.isLinkedPostsDataLoaded)
        {
            this.areAllFeedDataLoaded = true;
            this.mergeItemsFromAllSourceArrays();
            this.createFeedItems();
            this.renderFeed();
            this.bindFeedEvents();
        }
    },

    mergeItemsFromAllSourceArrays: function()
    {
        this.postsData = [];
        var maxItemsCountInSourceDataArrays = this.mostActivePostsData.length;

        if(this.lastPostsData.length > maxItemsCountInSourceDataArrays)
            maxItemsCountInSourceDataArrays = this.lastPostsData.length;

        if(this.linkedPostsData.length > maxItemsCountInSourceDataArrays)
            maxItemsCountInSourceDataArrays = this.linkedPostsData.length;

        for(var i = 0; i < maxItemsCountInSourceDataArrays; i++)
        {
            if(i < this.linkedPostsData.length)
            {
                var nextPostData = this.linkedPostsData[i];
                nextPostData.postType = "linkedPost";
                this.postsData.push(nextPostData);
            }

            if(i < this.lastPostsData.length)
            {
                var nextPostData = this.lastPostsData[i];
                nextPostData.postType = "lastPost";
                this.postsData.push(nextPostData);
            }

            if(i < this.mostActivePostsData.length)
            {
                var nextPostData = this.mostActivePostsData[i];
                nextPostData.postType = "mostActivePost";
                this.postsData.push(nextPostData);
            }
        }
    },

    createFeedItems: function()
    {
        this.feedItems = [];
        this.mostActivePostsItems = [];
        this.lastPostsItems = [];
        this.linkedPostsItems = [];

        var alreadyCreatedItems = {"photos": [], "spots": [], "speed": [], "videos": []};
        var isItemAlreadyCreated = function(type, id)
        {
            var isItemAlreadyCreated = false;
            for(var i = 0; i < alreadyCreatedItems[type].length; i++)
            {
                if(parseInt(alreadyCreatedItems[type][i], 10) == parseInt(id, 10))
                    isItemAlreadyCreated = true;
            }

            return isItemAlreadyCreated;
        }

        for(var i = 0; i < this.postsData.length; i++)
        {
            var isAddedItemCurrentlyViewed = false;
            if(php_vars.current_controller == this.postsData[i].type) 
            {
                if(this.viewedItemId == this.postsData[i].id)
                    isAddedItemCurrentlyViewed = true;
            }

            if(!isItemAlreadyCreated(this.postsData[i].type, this.postsData[i].id) && !isAddedItemCurrentlyViewed)
            {
                var outerHtml = "";

                outerHtml += "<a href='" + this.postsData[i].linkText + "'>"
                outerHtml += "  <div class='left'>";
                outerHtml += "      <img src='" + this.postsData[i].imagePath + "'>";
                outerHtml += "      <div class='label'><span>" + this.postsData[i].moduleName + "</span></div>";
                outerHtml += "  </div>";
                outerHtml += "  <div class='right'>";
                outerHtml += "      <div class='top'><div class='trim-to-parent'>";
                outerHtml += this.postsData[i].label;
                outerHtml += "      </div></div>";
                outerHtml += "      <div class='bottom'><span class='text'><span class='highlight'>";
                outerHtml += this.postsData[i].commentsCount + "</span> " + (this.postsData[i].commentsCount == 1 ? "comment" : "comments");
                outerHtml += "      </span></div>";
                outerHtml += "  </div>";
                outerHtml += "</a>";

                var $feedItem = $("<div/>");
                $feedItem.addClass("feedItem");
                $feedItem.html(outerHtml);
                this.feedItems.push($feedItem);

                if(this.postsData[i].postType == "linkedPost" && this.linkedPostsItems.length < this.maxLinkedPostsItemsCountPerInsideFeed)
                    this.linkedPostsItems.push($feedItem);
                else if(this.postsData[i].postType == "lastPost" && this.lastPostsItems.length < this.maxLastPostsItemsCountPerInsideFeed)
                    this.lastPostsItems.push($feedItem);
                else if(this.postsData[i].postType == "mostActivePost" && this.mostActivePostsItems.length < this.maxMostActivePostsItemsCountPerInsideFeed)
                    this.mostActivePostsItems.push($feedItem);

                alreadyCreatedItems[this.postsData[i].type].push(this.postsData[i].id);
            }
        }
    },

    bindFeedEvents: function()
    {
        var me = this;
        $.each($(".feedItem"), function() {
            $(this).on("mouseenter", function() {
                $(this).find(".right .top div").css({ color: "rgb(81,81,181)" });
            });

            $(this).on("mouseleave", function() {
                $(this).find(".right .top div").css({ color: "black" });
            });
        });

        $(window).on("resize.outsideFeed", function() {
            if(me.$outsideFeedLeftSideWrapper && me.$outsideFeedLeftSideWrapper.length > 0)
                me.updateOutsideFeedWrappersCSS();
        });
    },

    determineFeedType: function()
    {
        var viewportWidth = $(window).outerWidth();
        var requiredWidthForOutsideFeed = this.itemWidth * 2 + this.outsideFeedItemHorizontalMargin * 4;

        if(viewportWidth - 960 >= requiredWidthForOutsideFeed)
        {
            this.lastFeedMode = this.feedMode;
            this.feedMode = this.FEED_MODES.OUTSIDE;
        }
        else
        {
            this.lastFeedMode = this.feedMode;
            this.feedMode = this.FEED_MODES.INSIDE;
        }
    },

    renderFeed: function()
    {   
        this.determineFeedType();
        if(this.lastFeedMode == this.feedMode)
            return;

        this.createFeedWrapper();

        var me = this;

        if(this.feedMode == this.FEED_MODES.OUTSIDE)
        {
            var currentRow = 0;
            var i = 0;
            var itemsCount = this.feedItems.length;
            while(i < itemsCount)
            {
                this.$outsideFeedLeftSideWrapper.append(this.feedItems[i]);
                i++;
                this.$outsideFeedRightSideWrapper.append(this.feedItems[i]);
                i++;

                currentRow++;
                if(currentRow == this.outsideFeedItemsMaxCountPerOneSide)
                    break;
            }
            
            $("body").find(".feedItem").before("<div class='feedItemVerticalSpacer'></div>");
            $.each(this.$outsideFeedLeftSideWrapper.find(".feedItem"), function() {
                $(this).removeClass("feedItemFloat feedItemTransform feedItemHorizontalSpacer feedItemSmallHorizontalSpacer feedItemVerticalMargin").addClass("feedItemClear");
            });
            $.each(this.$outsideFeedRightSideWrapper.find(".feedItem"), function() {
                $(this).removeClass("feedItemFloat feedItemTransform feedItemHorizontalSpacer feedItemSmallHorizontalSpacer feedItemVerticalMargin").addClass("feedItemClear");
            });

            html_tools.trimifier.run("trim-feed-labels");
            $("body").find(".feedItem").addClass("feedItemTransform");
        }
        else if(this.feedMode == this.FEED_MODES.INSIDE)
        {
            this.$insideFeedBottomWrapper.css("display", "none");
            this.$insideFeedTopWrapper.css("display", "none");

            for(var i = 0; i < this.linkedPostsItems.length; i++)
                this.$insideFeedTopWrapper.find(".items").append(this.linkedPostsItems[i]);

            var feedItemNumber = 0;
            $.each(this.$insideFeedTopWrapper.find(".feedItem"), function() {
                $(this).removeClass("feedItemClear feedItemTransform").addClass("feedItemFloat feedItemVerticalMargin");

                if(feedItemNumber % 3 != 0)
                    $(this).addClass("feedItemHorizontalSpacer");
                else
                    $(this).addClass("feedItemSmallHorizontalSpacer");

                feedItemNumber++;
            });

            this.$insideFeedTopWrapper.append($("<div/>").addClass("feedItemBottomSpacer"));

            for(var i = 0; i < this.mostActivePostsItems.length; i++)
                this.$insideFeedBottomWrapper.find(".items").append(this.mostActivePostsItems[i]);
            for(var i = 0; i < this.lastPostsItems.length; i++)
                this.$insideFeedBottomWrapper.find(".items").append(this.lastPostsItems[i]);

            var feedItemNumber = 0;
            $.each(this.$insideFeedBottomWrapper.find(".feedItem"), function() {
                $(this).removeClass("feedItemClear feedItemTransform").addClass("feedItemFloat feedItemVerticalMargin");

                if(feedItemNumber % 3 != 0)
                    $(this).addClass("feedItemHorizontalSpacer");
                else
                    $(this).addClass("feedItemSmallHorizontalSpacer");

                feedItemNumber++;
            });

            this.$insideFeedBottomWrapper.append($("<div/>").addClass("feedItemBottomSpacer"));

            var afterSlide = function() {
                html_tools.trimifier.run("trim-feed-labels");
                $("body").find(".feedItem").addClass("feedItemTransform");
            }

            var topFinished = false;
            var bottomFinished = false;
            if(this.showInsideTopFeed)
            {
                this.$insideFeedTopWrapper.slideDown(300, function() {
                    topFinished = true;

                    if(topFinished && bottomFinished)
                        afterSlide();
                });
            }
            else
                topFinished = true;

            if(this.showInsideBottomFeed)
            {
                this.$insideFeedBottomWrapper.slideDown(300, function() {
                    bottomFinished = true;

                    if(topFinished && bottomFinished)
                        afterSlide();
                });
            }
            else
                bottomFinished = true;
        }
    },

    removePreviousFeed: function() 
    {
        if(this.$outsideFeedLeftSideWrapper)
        {
            this.$outsideFeedLeftSideWrapper.remove();
            this.$outsideFeedLeftSideWrapper = null;
        }

        if(this.$outsideFeedRightSideWrapper)
        {
            this.$outsideFeedRightSideWrapper.remove();
            this.$outsideFeedRightSideWrapper = null;
        }

        if(this.$insideFeedTopWrapper)
        {
            this.$insideFeedTopWrapper.remove();
            this.$insideFeedTopWrapper = null;
        }

        if(this.$insideFeedBottomWrapper)
        {
            this.$insideFeedBottomWrapper.remove();
            this.$insideFeedTopWrapper = null;
        }
    },

    createFeedWrapper: function()
    {
        this.removePreviousFeed();
        var viewportWidth = $(window).outerWidth();
        var requiredWidthForOutsideFeed = this.itemWidth * 2 + this.outsideFeedItemHorizontalMargin * 4;

        if(viewportWidth - 960 >= requiredWidthForOutsideFeed)
        {
            this.createOutsideFeedWrapper();
        }
        else
            this.createInsideFeedWrapper();
    },

    updateOutsideFeedWrappersCSS: function()
    {
        var headerHeight = 120;
        var outsideFeedHeight = $(document).outerHeight() - headerHeight - this.outsideFeedItemVerticalMargin;

        this.$outsideFeedLeftSideWrapper.css({
            "position": "absolute",
            "left": ($("#content").offset().left - this.outsideFeedItemHorizontalMargin - this.itemWidth) + "px",
            "top": (headerHeight) + "px",
            "width": this.itemWidth + "px",
            "height": outsideFeedHeight + "px"
        });

        this.$outsideFeedRightSideWrapper.css({
            "position": "absolute",
            "left": ($("#content").offset().left + 960 + this.outsideFeedItemHorizontalMargin) + "px",
            "top": (headerHeight) + "px",
            "width": this.itemWidth + "px",
            "height": outsideFeedHeight + "px"
        });

        var requiredHeightPerOneItem = this.itemHeight + this.outsideFeedItemVerticalMargin;
        this.outsideFeedItemsMaxCountPerOneSide = Math.floor(this.$outsideFeedLeftSideWrapper.outerHeight() / requiredHeightPerOneItem);
    },

    createOutsideFeedWrapper: function()
    {
        this.$outsideFeedLeftSideWrapper = $("<div/>");
        this.$outsideFeedRightSideWrapper = $("<div/>");
        $("#body-wrapper").prepend(this.$outsideFeedLeftSideWrapper);
        $("#body-wrapper").prepend(this.$outsideFeedRightSideWrapper);

        this.$outsideFeedLeftSideWrapper.addClass("trim-feed-labels");
        this.$outsideFeedRightSideWrapper.addClass("trim-feed-labels");

        this.updateOutsideFeedWrappersCSS();
    },

    createInsideFeedWrapper: function()
    {
        this.$insideFeedTopWrapper = $("<div/>");
        this.$insideFeedBottomWrapper = $("<div/>");
        $("#body-wrapper").find(".item-data .comments").before(this.$insideFeedTopWrapper);
        $("#body-wrapper").find(".item-data .comments").after(this.$insideFeedBottomWrapper);

        this.$insideFeedTopWrapper.addClass("insideFeedItems trim-feed-labels");
        this.$insideFeedBottomWrapper.addClass("insideFeedItems trim-feed-labels");

        createInsideFeedInnerHTML = function(count, label, sublabel)
        {
            var insideFeedInnerHTML  = "";
            insideFeedInnerHTML += "<div class='heading'>";
            insideFeedInnerHTML += "        <div class='count'>" + count + "</div>";
            insideFeedInnerHTML += "        <div class='label'>";
            insideFeedInnerHTML += "            <div class='big'>";
            insideFeedInnerHTML += "                " + label;
            insideFeedInnerHTML += "            </div>";
            insideFeedInnerHTML += "            <div class='small'>";
            insideFeedInnerHTML += "                " + sublabel;
            insideFeedInnerHTML += "            </div>";
            insideFeedInnerHTML += "        </div>";
            insideFeedInnerHTML += "</div>";
            insideFeedInnerHTML += "<div class='items'>";
            insideFeedInnerHTML += "</div>";

            return insideFeedInnerHTML;
        }

        var linkedDrivesCount = this.linkedPostsItems.length;
        var mostPopularDrivesCount = this.mostActivePostsItems.length + this.lastPostsItems.length;
        var insideFeedTopInnerHTML = createInsideFeedInnerHTML(linkedDrivesCount, "Linked drives", "Items, in which you might be interested.");
        var insideFeedBottomInnerHTML = createInsideFeedInnerHTML(mostPopularDrivesCount, "Most popular drives", "Most active and latest uploaded items.");

        this.$insideFeedTopWrapper.html(insideFeedTopInnerHTML);
        this.$insideFeedBottomWrapper.html(insideFeedBottomInnerHTML);

        if(linkedDrivesCount > 0)
            this.showInsideTopFeed = true;
        else
            this.showInsideTopFeed = false;

        if(mostPopularDrivesCount > 0)
            this.showInsideBottomFeed = true;
        else
            this.showInsideBottomFeed = false;
    }
}