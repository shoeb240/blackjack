var player_card_number;
var dealer_card_number;
var delay_player_multi;
var delay_dealer_multi;

$(document).ready(function() {

    startup_card();

    $("#play, #play2").click(function() {
        hide_startup_pop();
        clear_cards();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + 'index/play',
            success: function(json_data)
            {
                player_card_number = 1;
                dealer_card_number = 1;
                show_player_card(json_data.playerHand, json_data.playerPoints);
                show_dealer_card(json_data.dealerHand, json_data.dealerPoints);
                process_result(json_data);
            }
        });
    });


    $("#twist").click(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + 'index/twist',
            success: function(json_data)
            {
                show_player_card(json_data.playerHand, json_data.playerPoints);
                process_result(json_data);
            }
        });
    });

    $("#stick").click(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + 'index/stick',
            success: function(json_data)
            {
                show_dealer_card(json_data.dealerHand, json_data.dealerPoints);
                process_result(json_data);
            }
        });
    });

    $("#reset").click(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + 'index/reset',
            success: function(json_data)
            {
                clear_cards();
                clear_points();
                clear_stat();
                clear_history();
                hide_startup_pop();
            }
        });
    });

    $("#show_score1, #show_score2").click(function() {
        if ($(".scoreHistory").css("display") == "block") {
            $(".scoreHistory").hide();
        } else {
            if ($(".scoreHistory tbody").html() != '&nbsp;') {
                $(".scoreHistory").show();
            }
        }
    });
    
});

var diffX = 73;

function getCardX(number) {
    if (number == 1) calc = -1;
    else calc = -(1*(number-1)*diffX) -1;
    return calc;
}

function getCardY(group) {
    var groupArr = [];

    groupArr[0] = -1;
    groupArr[1] = -99;
    groupArr[2] = -197;
    groupArr[3] = -295;

    return groupArr[group];
}


function show_player_card(playerHand, playerPoints)
{
    var cardX;
    var cardY;
    var group_index;
    delay_player_multi = 1;
    var playerHandArr = playerHand.split(',');

    var playerHandArr_length = playerHandArr.length;
    for (index = (player_card_number-1); index < playerHandArr_length; ++index) {
        group_index = 0;
        rowIndex_index = playerHandArr[index];
        do {
            if (rowIndex_index > 13) rowIndex_index = rowIndex_index - 13;
            else break;
            group_index++;
        } while(1);

        cardX = getCardX(rowIndex_index);
        cardY = getCardY(group_index);

        $("#player_card" + player_card_number).css("background-position", cardX + "px " + cardY + "px");
        $("#player_card" + player_card_number).slideUp( 300 ).delay( 700*delay_player_multi ).fadeIn( 400 );
        
        player_card_number++;
        delay_player_multi++;
    }
    $("#player_score").html(playerPoints).slideUp( 1 ).delay( 700*(delay_player_multi-1) ).fadeIn( 400 );

}


function show_dealer_card(dealerHand, dealerPoints)
{
    var cardX;
    var cardY;
    var group_index;
    delay_dealer_multi = 1;
    var dealerHandArr = dealerHand.split(',');

    for (index = (dealer_card_number-1); index < dealerHandArr.length; ++index) {
        group_index = 0;
        rowIndex_index = dealerHandArr[index];
        do {
            if (rowIndex_index > 13) rowIndex_index = rowIndex_index - 13;
            else break;
            group_index++;
        } while(1);

        cardX = getCardX(rowIndex_index);
        cardY = getCardY(group_index);

        $("#dealer_card" + dealer_card_number).css("background-position", cardX + "px " + cardY + "px");
        $("#dealer_card" + dealer_card_number).slideUp( 300 ).delay( 500*delay_dealer_multi ).fadeIn( 400 );
        
        dealer_card_number++;
        delay_dealer_multi++;
    }
    $("#dealer_score").html(dealerPoints).slideUp( 1 ).delay( 700*(delay_dealer_multi-1) ).fadeIn( 400 );

}


function hide_play()
{
    $("#play_div").hide();
    $("#twist_div").show();
    $("#stick_div").show();
}

function show_play()
{
    var delayTime = 500 * (parseInt(delay_dealer_multi-1) + parseInt(delay_player_multi-1));
    $("#play_div").slideUp( 1 ).delay( delayTime ).fadeIn( 400 );
    $("#twist_div").hide();
    $("#stick_div").hide();
}

function hide_startup_pop()
{
    $(".messageBanner").hide();
    $("#messageBannerBox").hide();
    $(".scoreHistory").hide();
}

function show_startup_pop()
{
    var delayTime = 500 * (parseInt(delay_dealer_multi-1) + parseInt(delay_player_multi-1));
    $(".messageBanner").slideUp( 1 ).delay( delayTime ).fadeIn( 400 );
    $("#messageBannerBox").slideUp( 1 ).delay( delayTime ).fadeIn( 400 );
}

function process_result(json_data)
{
    if (json_data.result == 'win') {
        $(".message").html("You Won!");
    } else if (json_data.result == 'loose') {
        $(".message").html("You Lost!");
    } else if (json_data.result == 'draw') {
        $(".message").html("Draw!");
    } else {
        hide_play();
        hide_startup_pop();
    }

    if (json_data.result == 'win' || json_data.result == 'loose' || json_data.result == 'draw') {
        show_play();
        show_startup_pop();
        history_table_draw(json_data.history);
    }
    
    show_stat(json_data);
}

function history_table_draw(history)
{
    $(".scoreHistory tbody").html("<tr class='header'><td>Game</td><td>Player</td><td>Dealer</td><td>Result</td></tr>");
    var history_length = history.length;
    for(i = 0; i < history_length; i++) {
        htm = "<tr><td>"+(i+1)+"</td><td>"+history[i]['playerPoints']+"</td><td>"+history[i]['dealerPoints']+"</td><td>"+history[i]['result']+"</td></tr>";
        $(".scoreHistory tbody").append(htm);
    }
}

function calculate_percent(json_data, val)
{
    var total_play = parseInt(json_data.win) + parseInt(json_data.loose) + parseInt(json_data.draw);
    if (total_play == 0) return 0;
    percent = Math.round(parseInt(val) / parseInt(total_play) * 100);
    return percent;
}

function show_stat(json_data)
{
    percent = calculate_percent(json_data, json_data.win);
    $("#wins").html(json_data.win + " ("+parseInt(percent)+"%)");
    percent = calculate_percent(json_data, json_data.loose);
    $("#looses").html(json_data.loose + " ("+percent+"%)");
    percent = calculate_percent(json_data, json_data.draw);
    $("#draws").html(json_data.draw + " ("+percent+"%)");
}

function clear_cards()
{
    for(i=1; i<=7; i++) {
        $("#player_card" + i).hide();
        $("#dealer_card" + i).hide();
    }
}

function clear_points()
{
    $("#player_score").html('?');
    $("#dealer_score").html('?');
}

function clear_stat()
{
    $("#wins").html("0 (0%)");
    $("#looses").html("0 (0%)");
    $("#draws").html("0 (0%)");
}

function clear_history()
{
    $(".scoreHistory tbody").html("&nbsp;");
}

function startup_card()
{
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseUrl + 'index/startup',
        data:
        {
            'action':'play'
        },
        success: function(json_data)
        {
            player_card_number = 1;
            dealer_card_number = 1;
            show_player_card(json_data.playerHand, json_data.playerPoints);
            show_dealer_card(json_data.dealerHand, json_data.dealerPoints);
            process_result(json_data);
        }
    });

    function getInfo() {
        $.ajax({
                type: "POST",
                dataType: "json",
                url: baseUrl + 'index/info',
                success: function(json_data)
                {
                    show_stat(json_data);
                }
            });
    };
}

