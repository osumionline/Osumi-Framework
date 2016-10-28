(function(){
  'use strict';

  angular
    .module('BackendApp')
    .controller('ModelDetailFieldController', ModelDetailFieldController);

  ModelDetailFieldController.$inject = ['$scope'];
  function ModelDetailFieldController($scope){

    var vm = this;
    vm.value = $scope.item[$scope.field.name];

    vm.bool = false;
    vm.icon = '';

    if ($scope.field.type==7){
      vm.bool = true;
      vm.icon = (vm.value==1)?'on':'off';
    }

    if ($scope.refs[$scope.field.name]){
      if ($scope.refs[$scope.field.name].list['item_'+vm.value]!='') {
        vm.value = $scope.refs[$scope.field.name].list['item_' + vm.value];
      }
    }
  }
})();