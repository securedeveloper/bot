var myBot = angular.module("myBot", ['ngSanitize']);
myBot.controller("myBotController", function ($scope, $location, $anchorScroll, $http) {
    var getTime = function () {
        var dt = new Date();
        var hrs = dt.getHours();
        var min = dt.getMinutes();
        var sec = dt.getSeconds();
        var dtStr = ((hrs < 10) ? '0' : '') + hrs + ':';
        dtStr += ((min < 10) ? '0' : '') + min + ':';
        dtStr += ((sec < 10) ? '0' : '') + sec;
        return dtStr;
    };
    var userTypes = { bot: "bot", user: "other" };
    var userImages = { bot: "bot", user: "user1" };
    var comments = [{
        commentClass: 'bot',
        image: 'bot',
        isWelcome: true,
        welcomeMessage: 'Hi, YourBot here!',
        text: 'Hello, I\'m YourBot.I try to be helpful. (But I’m still just a bot.Sorry!) Type <b data-format - symbol="*" >&#65279;⁠⁠⁠something</b >&#65279; ⁠⁠⁠ to get started.<br/><br/>Please type \'help\' for Help!',
        time: getTime(),
    }];
    var message = "Type here!";
    $scope.message = message;
    var addComment = function (txt) {
        var comment = {
            commentClass: userTypes.user,
            image: userImages.user,
            isWelcome: false,
            welcomeMessage: "",
            text: txt,
            time: getTime(),
        };
        comments.push(comment);
        scrollTo('bottom');
    };
    var scrollTo = function (scrollTo) {
        $location.hash(scrollTo);
        $anchorScroll();
    };
    var getResponse = function (txt) {
        var comment = {
            commentClass: userTypes.bot,
            image: userImages.bot,
            isWelcome: false,
            welcomeMessage: "",
            text: "Loading...",
            time: getTime(),
            fullText: "",
            counter: 0
        };
        comments.push(comment);
        $http({ method: "GET", url: "http://my-bot.epizy.com/bot.php", params: { text: txt}}).then(function (response) {
            comment.fullText = response.data.response+"";
            typeWriter(comment);
            scrollTo('bottom');
        });
    };

    var typeWriter = function (comment) {
        comment.text = comment.fullText.slice(0, ++comment.counter);
        if (comment.text === comment.fullText) return;
        setTimeout(typeWriter(comment), 1000);
    };

    $scope.checkkey = function (event, msg) {
        if (!(typeof msg !== 'undefined' && msg))
            return;
        if (event.keyCode == 13) {
            addComment(msg);
            getResponse(msg);
            $scope.message = "";
        }
    };
    $scope.comments = comments;
});
