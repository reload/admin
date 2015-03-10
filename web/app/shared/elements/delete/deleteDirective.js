/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikDelete', ['$http', '$rootScope', function($http, $rootScope) {
  'use strict';

  return {
    restrict: 'E',
    replace: false,
    scope: {
      id: '@',
      type: '@'
    },
    link: function(scope) {
      // Handle clicks on numbers.
      scope.remove = function () {
        var result = window.confirm('Er du sikker på du vil slette dette? Handlingen kan ikke fortrydes.');
        if (result === true) {
          $http.delete('/api/' + scope.type + '/' + scope.id)
            .success(function() {
              $rootScope.$broadcast(scope.type + '-deleted', {});
            })
            .error(function() {
              alert('Sletning lykkes ikke!');
            });
        }
      };
    },
    templateUrl: 'app/shared/elements/delete/delete.html'
  };
}]);