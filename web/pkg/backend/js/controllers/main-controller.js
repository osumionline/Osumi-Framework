(function(){
  'use strict';

  angular
    .module('BackendApp')
    .controller('MainController', MainController);

  MainController.$inject = ['$location', '$mdDialog', '$mdMedia', '$mdSidenav', 'DataShareService', 'APIService'];
  function MainController($location, $mdDialog, $mdMedia, $mdSidenav, DataShareService, APIService){
    console.log('MainController');
    
    var vm = this;

    if (DataShareService.GetGlobal('token')==null){
      $location.path('/');
      return false;
    }

    vm.models        = [];
    vm.selectedModel = false;
    vm.selectModel   = selectModel;
    vm.openMenu      = openMenu;

    APIService.GetModels(function(response){
      vm.models = response.list;
    });

    function selectModel(name){
      vm.selectedModel = name;
      openMenu();
    }
    
    function openMenu(){
      $mdSidenav('leftmenu').toggle();
    }
  }
})();