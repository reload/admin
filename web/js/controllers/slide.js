/**
 * Slide controller. Controls the slide creation process.
 */
ikApp.controller('SlideController', function($scope, $location, $routeParams, slideFactory, templateFactory, imageFactory) {
    /**
     * Scope setup
     */
    $scope.steps = 4;
    $scope.slide = [];
    $scope.templates = templateFactory.getTemplates();

    $scope.style = {
        'height': '540px',
        'width': '940px',
        'fontsize': '18px'
    }

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
        if (!$routeParams.slideId) {
            // If the ID is not set, get an empty slide.
            $scope.slide = slideFactory.emptySlide();
            $scope.step = 1;
        } else {
            // Get the step.
            if ($routeParams.step) {
                if ($routeParams.step < 1 || $routeParams.step > $scope.steps) {
                    $location.path('/slide/' + $routeParams.slideId + '/1');
                    return;
                }
                else {
                    $scope.step = $routeParams.step;
                }
            }
            else {
                $scope.step = 1;
            }

            // Get slide.
            $scope.slide = slideFactory.getSlide($routeParams.slideId);

            if ($scope.slide === null) {
                $location.path('/slide');
                return;
            }

            // Make sure we are not placed at steps later than what is set in the data.
            var s = 1;
            if ($scope.slide.title !== '') {
                s = s + 1;
                if ($scope.slide.orientation !== '') {
                    s = s + 1;
                    if ($scope.slide.template !== '') {
                        s = s + 1;
                    }
                }
            }
            if ($scope.step > s) {
                $location.path('/slide/' + $scope.slide.id + '/' + s);
                return;
            }
        }
    }
    init();

    /**
     * Submit a step in the installation process.
     */
    $scope.submitStep = function() {
        $scope.slide = slideFactory.saveSlide($scope.slide);

        if ($scope.step < $scope.steps) {
            $location.path('/slide/' + $scope.slide.id + '/' + (parseInt($scope.step) + 1));
        } else {
            $location.path('/slides');
        }
    }

    /**
     * Set the orientation of the slide.
     * @param orientation
     */
    $scope.setOrientation = function(orientation) {
        $scope.slide.orientation = orientation;
    }

    /**
     * Validates that @field is not empty on slide.
     */
    function validateNotEmpty(field) {
        if (!$scope.slide) {
            return false;
        }
        return $scope.slide[field] !== '';
    }

    $scope.openToolbar = function(toolbar) {
        alert(toolbar);
    }

    /**
     * Handles the validation of the data in the slide.
     */
    $scope.validation = {
        titleSet: function() {
            return validateNotEmpty('title');
        },
        orientationSet: function() {
            return validateNotEmpty('orientation');
        },
        templateSet: function() {
            return validateNotEmpty('template');
        }
    };

    /**
     * Handles the state of the editor.
     */
    $scope.editor = {
        showTextEditor: false,
        toggleTextEditor: function() {
            $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
        },
        showBackgroundEditor: false,
        toggleBackgroundEditor: function() {
            $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
        }
    }

    /**
     * Sets the images from the factory.
     */
    $scope.backgroundImages = imageFactory.getImages();

    /**
     * Set the template id of a slide.
     * @param id
     */
    $scope.selectTemplate = function(id) {
        $scope.slide.template = id;
    }
});