(function(){
  angular
  .module('BackendApp')
  .directive('modelDetailField', function() {
    return {
      restrict: 'E',
      templateUrl: '/pkg/backend/partials/model-detail-field.html',
      scope: {
        field: '=',
        item: '=',
        refs: '='
      },
      transclude : false,
      controller: 'ModelDetailFieldController',
      controllerAs: 'vm'
    };
  });
})();