/*
Drupal.mavicsearch404 = Drupal.mavicsearch404 || {};

Drupal.behaviors.mavicsearch404 = function (context) {
$('#mavic-search-404-redirect-form').submit(function(e) {
	console.log('keyup');
  var code = e.keyCode || e.which; 
  if (code  == 13) {     
	  console.log('submitted');
    e.preventDefault();
    return false;
  }
});

};

Drupal.mavicsearch404Preventsubmit = function(e){
  console.log('submiter');


  console.log(e);
  if (e.keyCode == 13) {
	  
	  return false;
  }
  return true;
}

*/