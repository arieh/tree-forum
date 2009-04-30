function encrypt(){
	var pass = $('password').value
		,value='';
	var enc = hex_sha1(pass);
	for (i=0,l=pass.length;i<l;i++){
		value+=i;
	}
	$('password').value=value;
	$('password').name='bla';
	$('password').destroy();
	var password = new Element('input',{
		'name':'password',
		'type':'hidden',
		'value':enc
	})
	
	$('submit-dd').adopt(password);
	$('submit-dd').adopt(new Element('input',{
		'name':'encrypt',
		'type':'hidden',
		'value':0
	}));
	return true;
}
