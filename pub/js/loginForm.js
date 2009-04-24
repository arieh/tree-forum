/**
 * This file handles the hashing of the login form
 * 
 * this file does a 3 step encryption to the user-password to prevent data-fishing:
 * 	1. encrypting the password using the sha1 algorithm
 *  2. hashing the password with a temporary key suplied by the server and the user name
 *  3. re hashing the result
 *  
 * @require sha1.js
 * @author     XiroX <xiroxag@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */


/**setEncryption encrypts the login form before it is sent over the internet
 * @flow
 *     -getting form information
 *     -encryption of the password
 * @comment the script takes the folowing paramaters from the login form
 *     1. pass    : a password for the user
 *     2. tempKey : a temporary key sent by the server
 *     3. encrypt : this field will hold the hashed password 	
 * @access public
 */
function setEncryption(){
	//*****************
	//INTIALIZATION
	//*****************
	var name = document.getElementById('userName').value;//getting the user name
	var pass = document.getElementById('pass').value;//getting the original password
	var key  = document.getElementById('tempKey').value;//geting the temporary key
	//*****************
	//**************
	//ENCRYPTION 
	//**************
	//this part is so the password won't be sent with the page. the
	//substr is so the text field won`t apear to be change (not to confuse the end user)
	ename = encodeURI(name);
	//4 step encryption
	var encPass = hex_sha1(pass);   //encrypting the password
	//dbug.log(encPass);
	var string  = encPass+ename+key; //joining the key, the password and the user name
	var sha     = hex_sha1(string); //hashing them
	var enc     = hex_sha1(sha);    //re-hashing the password
	//******************
	var length  = pass.length;
	document.getElementById('pass').value=encPass.substr(0,length);
	document.getElementById('hash').value=enc;
}