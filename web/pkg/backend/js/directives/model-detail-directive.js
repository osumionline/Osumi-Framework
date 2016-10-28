(function(){
  angular
  .module('BackendApp')
  .directive('modelDetail', function() {
    return {
      restrict: 'E',
      templateUrl: '/pkg/backend/partials/model-detail.html',
      scope: {
        selected: '='
      },
      transclude : false,
      controller: 'ModelDetailController',
      controllerAs: 'vm'
    };
  });
})();