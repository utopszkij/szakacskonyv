const { execPath, hasUncaughtExceptionCaptureCallback } = require('process');
const inc = require('../vendor/viewTester.js');
inc.mock();

loadView('./includes/views/userbrowser.html', (vue) => {
    vue.errorMsg = '';
	vue.successMsg = '';
	vue.items = [{"id":1,"username":"test", "group":""}];
	
    describe('userBrowser', () => {
       it ('vue test', () => {
		   var w = vueTest(vue);		
	       expect(w).toBeTruthy();
       });
       it ('makePaginatorClass', () => {
	       var w = vue.makePaginatorClass(2,2);
	       expect(vue.errorMsg).toEqual('');
	       expect(w).toEqual('actPaginatorItem');
            /*
            .toEqual(expected) 
            .toNotEqual(expected)
            .toBeLess(expected) 
            .toBeLessOrEqual(expected) 
            .toBeGreater(expected) 
            .toBeGreaterOrEqual(expected) 
            .toBeDefined() 
            .toBeNotDefined() 
            .toBeTruthy() 
            .toBeFalsy() 
            .arrayContaining(expected) 
            .arrayNotContaining(expected) 
            .objectContaining(expected) 
            .objectNotContaining(expected)
            */
       });
     });
});
