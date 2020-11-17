function show_second_form(){
	second_form = document.querySelectorAll('.second-form');

	hide_first_form();
	show_iterate(second_form);

}

function hide_first_form(){
	first_form = document.querySelectorAll('.first-form');
	hide_iterate(first_form);
}

function show_iterate(iter){
	iter.forEach(function(x){
		x.style.display = "block";
	});
}

function hide_iterate(iter){
	iter.forEach(function(x){
		x.style.display = "none";
	});
}

document.querySelector("form input[type='submit']").onclick = function(e){
	if($(".second-form").is(":visible")){
		return true;
	}
	e.preventDefault();
	var website = document.querySelector("form > input[name='website']"),
	    email   = document.querySelector("form > input[name='email']");
	//show_second_form();
	//$(".first-form").toggle(1000);
	if(Boolean(website.value.trim(" ")) && Boolean(email.value.trim(" "))){

		$("form").animate({"height":"toggle", "overflow":"hidden"}, 1000).animate({"height":"toggle"}, 1000);
		$(".second-form")
		.attr("required", 1)
		.animate({"display":"none"}, 1200)
		.animate({"height":"40px"}, 
			200,
			function(){
				$(".first-form").css({"background":"#efefef", "border":"1px solid white"});
			});
		
	}else{
		alert("Please fill all form fields");
	}


	
}