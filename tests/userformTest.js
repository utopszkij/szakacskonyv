const { execPath, hasUncaughtExceptionCaptureCallback } = require('process');
const inc = require('../vendor/viewTester.js');
inc.mock();

loadView('./includes/views/userform.html', (vue) => {
    vue.errorMsg = '';
    vue.record = {"id":1, "username":"test1", 
        "realname":"test1", "email":"", "phone":"","avatar":"", "group":""};
    vue.loged = 2;
    vue.logedAdmin = true;
    vue.previous = "https://example.hu/?task=userek";
    
    describe('userForm', () => {
       it ('vue szintaktika test', () => {
		   var w = vueTest(vue);		
	       expect(w).toBeTruthy();
       });
       it ('delClick szintaktika test', () => {
	       vue.delClick();
	       expect(vue.errorMsg).toEqual('');
       });
    });
});
