function startApp() {
  angular
    .module('BackendApp', ['ngMaterial','ngRoute']);
}

function configApp() {
  angular
    .module('BackendApp')
    .config(function ($routeProvider, $locationProvider){

      $routeProvider
        .when('/', {
          templateUrl: '/pkg/backend/partials/login.html',
          controller: 'LoginController',
          controllerAs: 'vm'
        })
        .when('/main', {
          templateUrl: '/pkg/backend/partials/main.html',
          controller: 'MainController',
          controllerAs: 'vm'
        })
        .otherwise({redirectTo: '/'});
    });
}

startApp();
configApp();

function roundTo(num,round) {
  var resto = num%round;
  return Math.floor(num+round-resto);
}