<html ng-app="app">

<head>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.10/angular.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="style.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="print.css" media="print" />
</head>

<body>
  <?php @include_once("../../analyticstracking.php") ?>
    <header>
      <h1><i class="fa fa-lg fa-fw fa-check-square-o"></i> Chores App
        <span class="tagline">Quickly generate chore lists fairly and squarely</span>
      </h1>
    </header>


    <div id="main" ng-controller="InputController">

      <div class="input-wrapper">
        <div class="input-controls">
        <h2 style="display: inline-block;">What age and how many?</h2>
        <span class="description"></span>
      <form class="form">
      <div class="form-group">
        <label for="age" class="text-right col-sm-3 control-label">Age:</label>
        <div class="">
          <select ng-model="user_input.age" ng-options="n for n in [] | range:2:13" id="age" ng-change="go()"></select>
          <input type="checkbox" ng-model="user_input.age_specific_only" id="age_specific_only" ng-change="go()"> <label for="age_specific_only">age-specific chores only</label>
        </div>
      </div>
      <div class="form-group">
        <label for="age"  class="text-right col-sm-3 control-label">Chores:</label>
        <div class="">
          <select ng-model="user_input.num_chores" ng-options="n for n in [] | range:1:30" ng-change="go()"></select>
        </div>
      </div>
      <div class="form-group">
        <label for="display"  class="text-right col-sm-3 control-label">Display:</label>
        <a href="#r-table" onclick="return false;" aria-controls="table" role="tab" data-toggle="tab">Table</a> | <a href="#r-text" onclick="return false;" aria-controls="text" role="tab" data-toggle="tab">List</a> | <a href="#r-seqdia" onclick="return false;"
        aria-controls="seqdia" role="tab" data-toggle="tab">Seqdia</a>
      </div>
    </form>
  </div>
    <div class="explanation">Generated <b>{{user_input.num_chores}}</b> <span ng-if="user_input.age_specific_only">age-specific</span> chores for a <b>{{user_input.age}}-year old</b>.</div>
    </div>


      <div id="results" ng-if="finished_input.length">
        <h2 style="display: inline-block">Task List</h2>

        <!-- Nav tabs -->


        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane" id="r-text">

            <ol>
              <li ng-repeat="(key,value) in result">{{value}}</li>
            </ol>

          </div>
          <div role="tabpanel" class="tab-pane active" id="r-table">

            <table>
              <tr ng-repeat="(key,value) in result">
                <td><i class="fa fa-fw fa-square-o" ng-click="toggle_check_class($event)"></i></td>
                <td>{{key+1}}. {{value}}</td>
              </tr>
            </table>

          </div>
          <div role="tabpanel" class="tab-pane" id="r-seqdia">

            <div ng-repeat="(key,value) in result">
              A-->B: <b>{{value}}</b>
            </div>

          </div>
        </div>


      </div>



      <footer>
        <span ng-hide="!parseable">
          <button class="btn btn-link" ng-click="go()" title="Randomize List"><i class="fa fa-lg fa-refresh"></i> </button>
          <button class="btn btn-link" ng-click="download()" title="Download as CSV"><i class="fa fa-lg fa-download"></i> </button>
          <span style="font-size: .8em">Chores Randomly Generated: {{timestamp | date:'M/d/yy h:mm:ss a'}}</span>
        </span>


        <span class="credit"><a href="http://m.smithworx.com"><i class="fa fa-heart fa-lg fa-fw"></i>Matt Smith</a> <a href="http://github.com/smithworx/chores-app"><i class="fa fa-lg fa-fw fa-github-square" style="margin-left: 20px; font-size: 1.8em;"></i></a></span>
      </footer>

    </div>


    <script type="text/javascript">
      var myApp = angular.module('app', []);



      myApp.filter('range', function() {
        return function(input, min, max) {
          min = parseInt(min); //Make string input int
          max = parseInt(max);
          for (var i=min; i<=max; i++)
            input.push(i);
          return input;
        };
      });

      function sortObject(o) {
        var sorted = {},
          key, a = [];
        for (key in o) {
          if (o.hasOwnProperty(key)) {
            a.push(key);
          }
        }
        a.sort();
        for (key = 0; key < a.length; key++) {
          sorted[a[key]] = o[a[key]];
        }
        return sorted;
      }

      myApp.controller('InputController', function InputController($scope, $http, $location) {
        $scope.finished_input = [];
        $scope.submitting = false;
        $scope.parseable = true;
        $scope.toggle_check_class = function(evt){
          console.log("toggle_check_class"+evt.target);
          $this = $(evt.target);
          if( $this.hasClass('fa-square-o') ) {
            $this.addClass('fa-check-square-o');
            $this.removeClass('fa-square-o');
          } else {
            $this.addClass('fa-square-o');
            $this.removeClass('fa-check-square-o');
          }
        }
        $scope.canned_input = {
          "num_chores": 5,
          "age": 6,
          "age_specific_only": false
        };

        $scope.user_input = $scope.canned_input;

        $scope.result = {};

        $scope.go = function() {
          console.log("GO");
            $scope.submitting = true;
            $http.get('api.php', {
              params: $scope.user_input,
              cache: false
            }).
            success(function(data, status, headers, config) {
              // this callback will be called asynchronously
              // when the response is available
              if ($scope.canned_input !== $scope.user_input) {
                console.log("HELLO");
                //console.log("change path: "+btoa($scope.user_input));
                //$location.path(btoa(JSON.stringify($scope.user_input)));
              }
              $scope.timestamp = new Date();
              $scope.result = data;
              $scope.finished_input.push(data);
              $scope.submitting = false;
              $scope.parseable = true;
            }).
            error(function(data, status, headers, config) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              //console.log("ERROR");
              $scope.submitting = false;
              $scope.parseable = false;
            });
        };

        if ($location.path().substr(1).length) {
          console.log(atob($location.path().substr(1)));
          //$scope.user_input = JSON.parse(atob($location.path().substr(1)));
          //console.log($scope.user_input);
          //$scope.age = user_input.age;
          //$scope.num_chores = user_input.num_chores;
        }
        $scope.go();



        //Allows the user to download a .csv file
        $scope.download = function() {
          var download_data = sortObject($scope.result);
          var csvContent = "data:text/csv;charset=utf-8," + "#, TASK" + "\n";

          for (var i in download_data) {
            csvContent += (i+1) + ", " + download_data[i] + "\n";
          }

          var encodedUri = encodeURI(csvContent);
          var link = document.createElement("a");
          link.setAttribute("href", encodedUri);
          link.setAttribute("download", "list.csv");
          link.click();
        }
      });
    </script>

</body>

</html>
