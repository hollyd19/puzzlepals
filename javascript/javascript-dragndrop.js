$(document).ready(function () {
    var $box = $("#box"),
        $puzzle = $("#puzzle");
        
    if ($(".piece", $box).length != 0){
    // let the box items be draggable
    $(".piece", $box).draggable({
        cursor: "move",
        snap: ".place",
        snapMode: "corner"
    });
    }
    
    if($(".place", $puzzle).length != 0){
    $(".place", $puzzle).droppable({
        accept: ".piece",
        activeClass: "active",
        drop: function (event, ui) {
            // if we're moving away from a place, re-enable droppable
            if ($(ui.draggable).closest(".place")) {
                $(ui.draggable).closest(".place").droppable("enable");
            }
            // put the piece in the new place
            placePiece(this, ui.draggable);
            // disable droppable for this place so that more pieces can't be put here
            $(this).droppable("disable");
            // TODO send this data to the server so it can keep state about the game
            ////console.log("Piece " + $(ui.draggable).data("number") + " placed at position " + $(this).data("position"));
            //check_pieces_in_correct_location($(ui.draggable).data("number"), $(this).data("position"));
            // TODO check to see if i've won!
        }
    });
    }
    
    if($($box).length != 0){
    // let the box be droppable as well, accepting items from the puzzle so that i can put pieces back
    $box.droppable({
        accept: "#puzzle .piece",
        activeClass: "active",
        drop: function (event, ui) {
            // re-enable droppable for the place we're moving from
            $(ui.draggable).closest(".place").droppable("enable");
            // move the piece back to the box
            removePiece(ui.draggable);
        }
    });
    }
    
    // make the piece a child of the new place
    function placePiece($place, $piece) {
        $piece.appendTo($place);
    }
    
    function removePiece($item) {
        $item.appendTo($box);
    }
    
    /*IMPORTANT DO NOT DELETE: updates piece location on release of piece*/
    $(".piece").mouseup(function(){
	$(this).attr("id", "piece_moved"); 
	get_new_location();
	
    });
    
    /*DROPS ALL PUZZLE ON START OVER CLICK*/
    $("input[name=delete_all]").click(function(){
	drop_puzzle_collection(); 
    });
    
    /*DELETES PUZZLE CLICKED*/
    $(".start_puzzle_over").click(function(){
	var puzzle_name= $(this).attr("name");
	//console.log(puzzle_name);
	delete_puzzle(puzzle_name);
	//console.log('delete puzzle');
	return false;
    });
    
    /*LINKS TO TEST PUZZLE*/
    $(".resume_puzzle").click(function(){
	var puzzle_name= $(this).attr("name");
	console.log(puzzle_name); 
	$("input[name=\"in_prog_puzzle\"]").val(puzzle_name);
	console.log('resume puzzle');
	return true; 
    })
});


var array_of_place_locations= [];

/*function check_pieces_in_correct_location(piece_data_number, location_data_number){
    var add_it=false;
    var temp_piece_img='';
    var temp_piece='';
    $(".piece").each(function() {
        var temp_piece_location= $(this).offset();
        temp_piece= $(this);
        $.each(array_of_place_locations, function(index, value){
            if ((temp_piece_location.left < value[1]+5)
                &&(temp_piece_location.left < value[1]-5)
                &&(temp_piece_location.top < value[0]+5)
                &&(temp_piece_location.top < value[0]-5)
                &&(piece_data_number==location_data_number)){
                add_it=true;
                temp_piece_img= temp_piece.find('img');
            }
        });
    });
    if (add_it){
	dropped=true;
        var info=temp_piece_img.attr("src");
	var info_array=info.split("/");
	var puzzle_name=info_array[1];
	var piece_id=piece_data_number
        var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "update_correct_location", puzzle_name: puzzle_name, piece_id: piece_id, correct_location: "true"},
		global:false,
		success: function(data){
                ////console.log(data);
            } 
	});
        
        var ajaxquery2= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "query_correct_location", puzzle_name: puzzle_name},
		global:false,
		success: function(data){
                ////console.log(data);
                //if (data.indexOf('false') == -1) alert("Congrats! You have successfully completed the puzzle!");
            } 
	});
    } else {
	dropped= false;
        var info=temp_piece.find('img').attr("src");
        ////console.log(info);
        ////console.log(piece_data_number);
	var info_array=info.split("/");
	var puzzle_name=info_array[1];
	var piece_id=piece_data_number;
        var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "update_correct_location", puzzle_name: puzzle_name, piece_id: piece_id, correct_location: "false"},
		global:false,
		success: function(data){
                ////console.log(data);
            } 
	});
    }
}
*/

function size_places(width, height, num_pieces){
    $(".place").css("width", width);
    $(".place").css("height", height);
    $("#puzzle").css("width", width*(num_pieces+1));
    $("#puzzle").css("height", height*num_pieces+10);
    $("#box").css("height", height*num_pieces+50);
    var puzzle_top_left= $("#puzzle").offset();
    
    for(i=0; i<num_pieces; i++) {
        var temp1= puzzle_top_left.top + height*i;
        for (k=0; k<num_pieces; k++){
            var temp2= puzzle_top_left.left + width*k;
            var array= [temp1, temp2];
            array_of_place_locations.push(array);
        }
    }
}

function locate_pieces(){
    var top_left= $("#box").offset();
    var temp= $('#box').width()-$('.piece').width()+top_left.left;
    var temp2= $('#box').height()-$('.piece').height()+top_left.top;
    $(".piece").each(function() {
        var left_temp= getRandomInt(top_left.left, temp, true);
        var top_temp= getRandomInt(top_left.top, temp2, false);
        $(this).css({position:'absolute', left: left_temp, top: top_temp});
    });
    
}

function place_moved_pieces() {
   $(".piece").each(function() {
        var id= $(this).attr("id");
	id=id.split("~");
	var x= id[0]+"px";
	var y= id[1]+ "px";
        $(this).css({position:'absolute', left: x, top: y});
    });
}

function getRandomInt (min, max, left) {
    var result= Math.floor(Math.random() * (max-min +1)) +min
    var left_side= $('#puzzle').offset().left - $('.piece').width();
    var right_side= $('#puzzle').offset().left + $('#puzzle').width();
    if (result > left_side && result < right_side && left && left){
        if (result < $("#box").offset().left + $('#puzzle').width()/2){
            result= getRandomInt(min, left_side, left);
        } else {
            result= getRandomInt(right_side, max, left);
        }
        
    }
    return result
}

function get_all_piece_info(){
	var puzzle_name=$("input[name=p-id]").val();
		console.log(puzzle_name); 
	$('.piece img').each(function(){
		var x=$(this).offset().left;
		var y=$(this).offset().top;
		var info=$(this).attr("src");
		var info_array=info.split("/");
		var piece_id_info=info_array[2];
		piece_id_info=piece_id_info.split(".");
		var piece_id=piece_id_info[0];
		add_to_database(puzzle_name, piece_id, x, y);		
	});
}

function add_to_database(puzzle_name, piece_id, x, y){
	var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "add_piece", puzzle_name: puzzle_name, piece_id: piece_id, x: x, y: y},
		global:false
	});
}

function get_new_location() {
	var p= $('#piece_moved img');
	var x=p.offset().left;
	var y=p.offset().top;
	var info=p.attr("src");
	var info_array=info.split("/");
	var puzzle_name=$("input[name=p-id]").val();
	var piece_id_info=info_array[2];
	piece_id_info=piece_id_info.split(".");
	var piece_id=piece_id_info[0];
	if (in_correct_location(piece_id, x, y)) {
	    update_location(puzzle_name, piece_id, x, y, true);
	}
	else{
	    update_location(puzzle_name, piece_id, x, y, false);
	}
	
}

function in_correct_location(piece_id, x, y) {
    correct_x=false;
    correct_y=false;
    name="#"+piece_id+""; 
    place_y=$(name).offset().top;
    place_x=$(name).offset().left;
    if(place_y<=y+20 && place_y >= y+15){
	correct_y=true; 
    }
    if (place_x<=x+2 && place_x>x-2) {
	correct_x=true;
    }
  
    if (correct_x  && correct_y) {
	return true;
    }
    else return false; 
}

function update_location(puzzle_name, piece_id, x, y, correct_location) {
    var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "update_location", puzzle_name: puzzle_name, piece_id: piece_id, x: x, y: y, correct_location: correct_location},
		global:false,
		success: function(data){
                console.log(data);
            } 
	});
    $('#piece_moved').removeAttr("id");
}




function get_pieces(puzzle_name) {
    ////console.log(puzzle_name);
     var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "return_puzzle", puzzle_name: puzzle_name},
		global:false,
		success: function(result){
		   add_id_and_place(result);
		   
                }
	});
}

function add_id_and_place(data) {
    var pieces=data.split("~!@#$%^&*");
    for (i=0; i<pieces.length-4; i+=4) {
	$(".piece[data-number="+pieces[i+3]+"]").attr("id", pieces[i+1]+"~"+pieces[i+2]); 
    }
    
    place_moved_pieces(); 
    //code
}

function delete_puzzle(puzzle_name) {
    var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "delete_puzzle", puzzle_name: puzzle_name},
		global:false,
		success: function(data){
		    if (data=="success") {
				$("#" + puzzle_name).fadeOut(100);
			
		    }
		} 
     
	});  
}

function drop_puzzle_collection() {
    var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "drop_collection"},
		global:false,
                success: function(data){
		    if (data=="success") {
			////console.log("wooooo");
                        document.location.reload(true);
						
                    }
                }
	});  
}

function enable_scrollbar() {
    if ($('.scrollable_div').length != 0){
	$('.scrollable_div').slimScroll({
		color: '#00f',
		size: '8px',
		height: '280px',
		width: '190px',
		alwaysVisible: true,
		railVisible: true,
		railOpacity: 0.1
	});
	
	$('#pick_a_photo').slimScroll({
		color: '#00f',
		size: '8px',
		height: '300px',
		width: '190px',
		alwaysVisible: true,
		railVisible: true
	});
    }
}

//function sendRequestToRecipients() {
	//var user_ids = document.getElementsByName("user_ids")[0].value;
	//alert(user_ids);
	//FB.ui({method: 'apprequests', message: 'Request message goes here', to: user_ids}, requestCallback);
//}

//function sendRequestViaMultiFriendSelector() {
	//FB.ui({method: 'apprequests', message: 'MFS message goes here'}, requestCallback);
//}

//function requestCallback(response){
	//Handle callback here
	//if((response != null) &&(response != false)) {
      //alert('User Accepts');
    //}
//}

$('#create_puz_div').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})

$('#ongoing_puz_div').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})

$('#completed_puz_div').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})


function check_pieces(){
    var puzzle_id=$("input[name=p-id]").val();
    var ajaxquery= $.ajax({
		url : "functions.php",
		type: "POST",
		data: {function_name: "check_pieces", pID: puzzle_id},
		global:false,
                success: function(data){
		    console.log(data);
		}
    });
    
}

