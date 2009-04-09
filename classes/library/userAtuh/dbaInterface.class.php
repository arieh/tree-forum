<?php
/*
	Permission is hereby granted, free of charge, to any person obtaining
	a copy of this software and associated documentation files (the
	"Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:
	
	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
	CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
	TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


interface dbaInterface {

    /**
     * insertes the key that was generated for this session into the database
     * 
     * @param string $key a random key
     * 
     * @access public
     */
   	public function insertKey($key);
      
    /**
     * retrives the last key that was stored for a specific ip
     * 
     * @param string $ip an ip address
     * @access public
     * @return string the key that was generated for this ip 
     */
    public function getKeyFromDB($ip);
    
    /**
     * checks if a specific user exists
     * 
     * @param string $name a user name
     * 
     * @uses $GLOBALS['_suCnfgs_'] a global variable that holds database structure information
     * @access public
     * @return bool whether a user with this name exists
     */
    public function userExists($name);
    
    /**
     * retrives a password from the database for a specific user
     *
     * @param string $name a user name
     * 
     * @uses $GLOBALS['_suCnfgs_'] a global variable that holds database structure information
     * @access public
     * @return string the password for that user
     */
    public function getPass($name);
}
?>