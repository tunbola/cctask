/**
 * Created by tunbola.ogunwande on 28/08/2016.
 * An angular module for a PCP tab on the Contact Display page
 */
(function (angular, $, _) {
    var resourceUrl = CRM.resourceUrls['com.civicrm.contactpcptab'];
    var app = angular.module('pcp', ['ngRoute', 'crmResource']);

    app.config(['$routeProvider',
        function($routeProvider){
        $routeProvider.when('/campaign_pages/:contact_id', {
                templateUrl :'~/pcp/pcp.html',
                controller : 'CampaignPageCtrl'
            });
    }]);

    app.controller('CampaignPageCtrl', function($scope, $http, $routeParams, crmApi){
        $scope.contact_id = $routeParams.contact_id;
        $scope.total_campaigns = '';
        $scope.campaigns = {};

        $scope.pcp_status_types = [];
        $scope.contribution_store = {};

        //for translations, would need this later for translations
        $scope.ts = CRM.ts('com.civicrm.contactpcptab');

        $scope.formatMoney = function(value){
            return CRM.formatMoney(value, false);
        };

        //utility function to form various URL links
        $scope.formUrlLink = function(value, type){
            if(type=="page_link"){
                return path = CRM.url('civicrm/pcp/info', {reset: 1, id: value.id});
            }
            else if(type=='contribution_page_link'){

                if(value.page_type =='contribute'){
                    return path = CRM.url('civicrm/'+ value.page_type + '/transact', {reset: 1, id: value.page_id});
                }
                else{
                    return path = CRM.url('civicrm/' + value.page_type + '/register', {reset: 1, id: value.page_id});
                }
            }
            else if(type=='edit_link'){
                return path = CRM.url('civicrm/pcp/info', {reset: 1, action:'update', id: value.id, context: 'dashboard' });
            }
            else if(type == "contributors_link"){
                return path = CRM.url('civicrm/contribute/search', {reset: 1, pcp_id:value.id, force: 1 });

            }
        }

        //calculate total amount raise for a contribution from value field in contribution_store for a particular pcp
        $scope.calculateAmountRaised = function(key){
            if($scope.contribution_store[key].count > 0){
                var total = 0;
                $scope.contribution_store[key].values.forEach(function(v){
                   total += parseInt(v.amount);
                })
                return $scope.formatMoney(total);
            }
            return $scope.formatMoney(0);
        };

        //Get PCP's for a user with associated contributions
        var params = {"contact_id":$scope.contact_id,"api.ContributionSoft.get":{"pcp_id":"$value.id"},"api.ContributionPage.get":{"id":"$value.page_id"}, "sequential":1};
        crmApi('PCP', 'get', params)
            .then(function(result) {
            if(result.is_error == 0){
            $scope.campaigns = result;
            $scope.total_campaigns = Object.keys(result.values).length;
            console.log(result);
            }
            var contribution_store = {};
            for(var key in result.values){
                contribution_store[key] = {};
                if(!result.values.hasOwnProperty(key)) continue;
                contribution_store[key].count = result.values[key]["api.ContributionSoft.get"]["count"];
                contribution_store[key].values = result.values[key]["api.ContributionSoft.get"]["values"];
                contribution_store[key].contribution_page = result.values[key]["api.ContributionPage.get"]["values"][0].title;
            }

            $scope.contribution_store = contribution_store;

        })

       //Get PCP status types
        crmApi('PCP', 'getoptions',{entity:'PCP',params:{field:"status_id"}})
            .then(function(result) {
                if(result.is_error == 0){
                $scope.pcp_status_types = result.values;
                }
            })
    });


})(angular, CRM.$, CRM._);