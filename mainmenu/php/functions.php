<?php 
function randomwords(){
	$rwordsstr="Lorem ipsum dolor sit amet, consectetur adipiscing elit. In hendrerit scelerisque vulputate. Proin ut orci sapien. Curabitur consectetur ornare est ac eleifend. Vivamus at purus id nibh iaculis tincidunt. Duis id aliquet risus. In vestibulum, sapien eu mattis varius, orci leo feugiat lacus, sed fermentum dui ipsum sit amet urna. Duis in elit id velit sodales elementum vitae sit amet nisi. Vivamus porta lacinia consequat. Mauris nec dui ut quam rutrum ultricies. Nullam ac ornare lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi at odio turpis, at pharetra eros. Proin lacinia egestas suscipit. Donec nibh dui, congue a dictum a, egestas ut risus. Phasellus non neque lorem.Aenean metus leo, lacinia at blandit id, pretium at erat. Maecenas mollis mi vitae erat adipiscing vestibulum adipiscing nulla suscipit. Pellentesque ut ipsum a urna ultrices pellentesque eu nec elit. Vivamus ac dui tortor, vitae gravida mauris. Aenean pharetra mauris eget mi cursus venenatis. Vestibulum neque dolor, venenatis ac gravida sed, porta varius eros. Etiam lobortis, mi at tincidunt dictum, felis felis interdum felis, at vehicula est tellus sed purus. Nulla facilisi. Suspendisse vitae leo enim, nec pharetra justo. Fusce odio mi, convallis ac rutrum sit amet, vehicula nec nunc. ";
	$lines=explode(".",$rwordsstr);

	shuffle($lines);
	$lorem = '';
	foreach($lines as $l => $i){
		$lorem .= $i;
	}

	return $lorem;
}

 ?>