(function(){
  'use strict';

  angular
    .module('BackendApp')
    .controller('LoginController', LoginController);

  LoginController.$inject = ['$location', '$mdDialog', 'DataShareService', 'APIService'];
  function LoginController($location, $mdDialog, DataShareService, APIService){
    console.log('LoginController');
    
    var vm = this;
    vm.user = '';
    vm.pass = '';
    
    vm.login = login;
    
    function login(){
      APIService.Login(vm.user,vm.pass,loginSuccess);
    }
    
    function loginSuccess(response){
      if (response.status=='ok'){
        DataShareService.SetGlobal('token',response.token);
        $location.path('/main');
      }
      else{
        $mdDialog.show(
          $mdDialog.alert()
            .parent(document.body)
            .clickOutsideToClose(true)
            .title('Error')
            .textContent('El nombre de usuario o contraseña son incorrectos.')
            .ariaLabel('Error')
            .ok('OK')
        );
      }
    }
  }
})();