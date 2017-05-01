<?php
namespace Xodebox;

Config::$routerConfig = [
    'map' => [

        [
            'url'         =>   'agent/testpdf',
            'controller'  =>   'AgentPanel',
            'action'      =>   'testPDF'
        ],

        
        [
            'url'         =>   'invoice/:invoice_id',
            'controller'  =>   'Invoice',
            'action'      =>   'viewInvoice'
        ],

        [
            'url'         =>   'invoice/:invoice_id/download',
            'controller'  =>   'Invoice',
            'action'      =>   'downloadInvoice'
        ],
    	

        //========== PUBLIC LEVEL ===========
        [ 'url'          => 'index',
          'controller'   => 'AgentPanel',
          'action'       => 'index'
        ],

        [ 'url'          => 'public/download/$link',
          'controller'   => 'PublicAccess',
          'action'       => 'download'
        ],
    		

    	[
    			'url'         => 'public/reset/$hlink',
    			'controller'  => 'PublicAccess',
    			'action'      => 'resetPassword'
    	],
        
        

        //===========  Company Panel ========================
        [
            'url'         =>   'company',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'index'
        ],
        [
            'url'         =>   'company/login',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'login'
        ],
        [
            'url'         =>   'company/logout',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'logout'
        ],
		
		 [
            'url'         =>   'company/recover',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'recoverPass'
        ],

        /*
		 [
            'url'         =>   'company/form',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'showForm'
            ],*/
        
        [
            'url'         =>   'company/product/new',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'newProduct'             //Create a new product
        ],
		
		 [
            'url'         =>   'company/invoice/view',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'listInvoices'             //List Invoice
        ],

        [
            'url'         =>   'company/product/:product_id/edit',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'editProduct'             //Edit a product
        ],
        
        [
            'url'         =>   'company/product/:product_id/update',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'updateProduct'             //Update product
        ],
        
        [
            'url'         =>   'company/product',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'listProducts'
        ],

		
		 [
            'url'         =>   'company/dashboard',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'viewDashboard'
        ],

        // ----- Company --> Agent methods
        [
            'url'         =>   'company/agent',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'listAgents'
        ],
        

        [
            'url'         =>   'company/agent/:agent_id/hasproduct/:product_id',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'agentHasProduct'
        ],
        
        [
             'url'         =>   'company/agent/search',
             'controller'  =>   'CompanyPanel',
             'action'      =>   'searchAgents'
         ],
		 
		  [
             'url'         =>   'company/form/search',
             'controller'  =>   'CompanyPanel',
             'action'      =>   'searchForms'
         ],

         [
            'url'         =>   'company/assign',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'assignProducts'
        ],

        [
            'url'         =>   'company/product/:product_id/agent/:agent_id/assign',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'productAssign'
        ],

        [
            'url'         =>   'company/product/:product_id/agent/:agent_id/remove',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'productUnassign'
        ],
		
        [
            'url'         =>   'company/product/:product_id',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'showProduct'
        ],
		  [
            'url'         =>   'company/profile',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'editProfile'
        ],
        
        [
            'url'         =>   'company/product/create',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'createProduct'
        ],

        // ---- Company invoice methods
        [
            'url'         =>   'company/invoice',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'listInvoices'
        ],

        [
            'url'         =>   'company/invoice/:invoice_id',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'viewInvoice'
        ],

        [
            'url'         =>   'company/invoice/:invoice_id/approve',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'approveInvoice'
        ],

        [
            'url'         =>   'company/invoice/:invoice_id/unapprove',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'unapproveInvoice'
        ],


        
        [
            'url'         =>   'company/invoice/edit',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'editInvoiceTemplate'
        ],

        [
            'url'         =>   'company/invoice/update',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'updateInvoiceTemplate'
        ],

		//dynamic forms
        [
            'url'         =>   'company/product/:product_id/form/new',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'createProductForm'
        ],

        
        [
            'url'         =>   'company/product/:product_id/form/:form_id/edit',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'editProductForm'
        ],

		[
            'url'         =>   'company/product/:product_id/existingForm/:form_id/searchEdit',    //Load existing form template into a new form
            'controller'  =>   'CompanyPanel',
            'action'      =>   'searchEditForm'
        ],
		
		[
            'url'         =>   'company/product/:product_id/existingForm/:form_id/searchNew/:targetForm',    //Load existing form template into an existing form
            'controller'  =>   'CompanyPanel',
            'action'      =>   'searchEditForm'
        ],
        
        [
            'url'         =>   'company/product/:product_id/form/:form_id/remove',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'removeProductForm'
        ],
          
        //Create a new dynamic form
        [ 
            'url'         =>   'company/product/:product_id/form/save',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'saveProductForm'
        ],
        
        //Save existing dynamic form
        [
            'url'         =>   'company/product/:product_id/form/:form_id/save',
            'controller'  =>   'CompanyPanel',
            'action'      =>   'saveProductForm'
        ],

        //---------- Company AJAX Responders
        [
            'url'        =>   'company/checksku',
            'controller' =>   'CompanyPanel',
            'action'     =>   'checksku'
        ],
        


        //----------- PRODUCT CONTROLLER -------------------
        /*        [
            'url'         =>   'product/create',
            'controller'  =>   'Product',
            'action'      =>   'create'
            ],*/



        //===========  Agent Panel   ========================
        [
            'url'         =>   'agent/profile',
            'controller'  =>   'AgentPanel',
            'action'      =>   'viewProfile'
        ],
		
		 [
            'url'         =>   'agent/recover',
            'controller'  =>   'AgentPanel',
            'action'      =>   'recoverPass'
        ],
        
        [
            'url'         =>   'agent',
            'controller'  =>   'AgentPanel',
            'action'      =>   'index'
        ],

        [
            'url'         =>   'agent/sales',
            'controller'  =>   'AgentPanel',
            'action'      =>   'viewSales'
        ],
		
		[
            'url'         =>   'agent/downline',
            'controller'  =>   'AgentPanel',
            'action'      =>   'viewDownlines'
        ],

        [
            'url'         =>   'agent/agency/client',
            'controller'  =>   'AgentPanel',
            'action'      =>   'viewClients'
        ],

        
        [
            'url'         =>   'agent/client/search',
            'controller'  =>   'AgentPanel',
            'action'      =>   'searchClients'
        ],

        [
            'url'         =>   'agent/company/search',
            'controller'  =>   'AgentPanel',
            'action'      =>   'searchCompany'
        ],
		
        // ------------AGENT CLIENT ACTIONS ----------
        [
            'url'         =>   'agent/client',
            'controller'  =>   'AgentPanel',
            'action'      =>   'listClients'
        ],
        [
            'url'         =>   'agent/client/new',
            'controller'  =>   'AgentPanel',
            'action'      =>   'newClient'
        ],
        [
            'url'         =>   'agent/client/create',
            'controller'  =>   'AgentPanel',
            'action'      =>   'createClient'
        ],
        
        [
            'url'         =>   'agent/client/:client_id/edit',
            'controller'  =>   'AgentPanel',
            'action'      =>   'editClient'
        ],
        
        [
            'url'         =>   'agent/client/:client_id/update',
            'controller'  =>   'AgentPanel',
            'action'      =>   'updateClient'
        ],

        // ------------- AGENT INVOICE ACTIONS -------------
        [ //Request for a invoice selection screen
            'url'         =>   'agent/invoice/new',
            'controller'  =>   'AgentPanel',
            'action'      =>   'newInvoice'
        ],

        [ //Request for a invoice selection screen given the client
            'url'         =>   'agent/invoice/new/client/:client_id',
            'controller'  =>   'AgentPanel',
            'action'      =>   'newInvoice'
        ],

        [//Request for a draft invoice
            'url'         =>   'agent/company/:company_id/client/:client_id/invoice/new',
            'controller'  =>   'AgentPanel',
            'action'      =>   'newInvoice'
        ],


        [ //Create invoice from a draft
            'url'         =>   'agent/company/:company_id/client/:client_id/invoice/create',
            'controller'  =>   'AgentPanel',
            'action'      =>   'createInvoice'
        ],


        [ //View Invoice --> Process AJAX request to update Invoice Form
            'url'          => 'agent/form/:form_id/save',
            'controller'   => 'AgentPanel',
            'action'       => 'saveInvoiceForm'
        ],
        
        [ //Send file attachment
            'url'          => 'agent/invoice/:invoice_id/attachment',
            'controller'   => 'AgentPanel',
            'action'       => 'invoiceAttachmentForm'
        ],


        // -----------AGENT INVOICE ACTIONS ----------        
        [
            'url'         =>   'agent/invoice',
            'controller'  =>   'AgentPanel',
            'action'      =>   'listInvoice'
        ],
        [
            'url'         =>   'agent/invoice/new',
            'controller'  =>   'AgentPanel',
            'action'      =>   'newInvoice'
        ],

        [ //Download Invoice
            'url'         =>   'agent/invoice/:invoice_id/download',
            'controller'  =>   'AgentPanel',
            'action'      =>   'downloadInvoice'
        ],

        [ //View existing invoice
            'url'          => 'agent/invoice/:invoice_id',
            'controller'   => 'AgentPanel',
            'action'       => 'viewInvoice'
        ],

        [ //Update existing invoice
            'url'          => 'agent/invoice/:invoice_id/edit',
            'controller'   => 'AgentPanel',
            'action'       => 'editInvoice'
        ],

        
        [ //Update existing invoice
            'url'          => 'agent/invoice/:invoice_id/update',
            'controller'   => 'AgentPanel',
            'action'       => 'updateInvoice'
        ],

        


        
        // -------------AGENT SESSION ACTIONS ----------
        [
            'url'         =>   'agent/login',
            'controller'  =>   'AgentPanel',
            'action'      =>   'login'
        ],
        [
            'url'         =>   'agent/logout',
            'controller'  =>   'AgentPanel',
            'action'      =>   'logout'
        ],

        //------- Agent AJAX Responders
        [
            'url'         =>   'agent/client/checkuid',
            'controller'  =>   'AgentPanel',
            'action'      =>   'checkClientID'
        ],

        [
            'url'         =>   'agent/invoice/:invoice_id/mail',
            'controller'  =>   'AgentPanel',
            'action'      =>   'mailInvoice'
        ],
    		

    	[
    		'url'         =>   'agent/invoice/:invoice_id/mailall',
    		'controller'  =>   'AgentPanel',
    		'action'      =>   'mailInvoiceAll'
    	],
    		
        
       
        //======  Admin Panel ==============================
        [
            'url'         =>   'admin',
            'controller'  =>   'AdminPanel',
            'action'      =>   'index'
        ],
		
    	/*	//admin panel has no password recover option
		[
            'url'         =>   'admin/recover',
            'controller'  =>   'AdminPanel',
            'action'      =>   'recoverPass'
        ],*/

        [
            'url'        =>   'admin/login',
            'controller' =>   'AdminPanel',
            'action'     =>   'login'
        ],

        [
            'url'        =>   'admin/statistic',
            'controller' =>   'AdminPanel',
            'action'     =>   'statistic'
        ],

        // --------- ADMIN PANEL Invoice Functions
        [
            'url'        =>   'admin/invoice/:invoice_id',
            'controller' =>   'AdminPanel',
            'action'     =>   'viewInvoice'
        ],

        // -------- ADMIN PANEL AGENT FUNCTIONS 
        [
            'url'        =>   'admin/agent/new',
            'controller' =>   'AdminPanel',            
            'action'     =>   'newAgent'
        ],

        //View agent info (extra function);
        [
            'url'        =>   'admin/agent/:agent_id',
            'controller' =>   'AdminPanel',            
            'action'     =>   'viewAgent'
        ],


         [
             'url'        =>   'admin/agent/create',
             'controller' =>   'AdminPanel',
             'action'     =>   'createAgent'
         ],
        
        [
            'url'        =>   'admin/agent',
            'controller' =>   'AdminPanel',
            'action'     =>   'listAgent'
        ],

        //JSON Responder, use post request only
        [
            'url'        =>   'admin/agent/search',
            'controller' =>   'AdminPanel',
            'action'     =>   'searchAgents'
        ],

        [
            'url'        =>   'admin/agent/search_free',
            'controller' =>   'AdminPanel',
            'action'     =>   'searchAvailableAgents'
        ],

        [
            'url'        =>   'admin/leader/search',
            'controller' =>   'AdminPanel',
            'action'     =>   'searchLeader'
        ],

        /// --------- ADMIN: AGENT ASSIGNMENT FUNCTIONS --------------
        [
            'url'        =>   'admin/agent/assign/:agent_id/to/:leader_id',
            'controller' =>   'AdminPanel',
            'action'     =>   'assignLeader'
        ],

        [
            'url'        =>   'admin/agent/:agent_id/makeleader/agency/:agency_id',
            'controller' =>   'AdminPanel',
            'action'     =>   'makeLeader'
        ],


        [
            'url'        =>   'admin/agent/:agent_id/makeleader',
            'controller' =>   'AdminPanel',
            'action'     =>   'makeLeader'
        ],

        [
            'url'        =>   'admin/agent/:agent_id/removeLeader',
            'controller' =>   'AdminPanel',
            'action'     =>   'removeLeader'
        ],

                [
            'url'        =>   'admin/agent/:agent_id/removeLeader',
            'controller' =>   'AdminPanel',
            'action'     =>   'assignLeader'
        ],

        ['url'           =>  'admin/unassign/agent/:agent_id',
         'controller'    =>  'AdminPanel',
         'action'        =>  'removeLeader'],
        

        //---------- ADMIN SESSION FUNCTIONS -------------
        [
            'url'        =>   'admin/logout',
            'controller' =>   'AdminPanel',
            'action'     =>   'logout'
        ],

        //---- ADMIN : Sales progress
        [
            'url'        =>   'admin/sales-progress',
            'controller' =>   'AdminPanel',
            'action'     =>   'progress'
        ],

        [
            'url'        =>   'admin/sales-progress/agent',
            'controller' =>   'AdminPanel',
            'action'     =>   'progress'
        ],

        
        [
            'url'        =>   'admin/sales-progress/agent/:agent_id',
            'controller' =>   'AdminPanel',
            'action'     =>   'progress'
        ],

        
        [
            'url'        =>   'admin/sales-progress/date',
            'controller' =>   'AdminPanel',
            'action'     =>   'progressByDate'
        ],
        
        [
            'url'        =>   'admin/sales-progress/sales',
            'controller' =>   'AdminPanel',
            'action'     =>   'progressBySales'
        ],
        

        ['url'        =>   'admin/profile',
         'controller' =>   'AdminPanel',
         'action'     =>   'profile'],
        
        
        ['url'           =>  'admin/organization/create',
         'controller'    =>  'AdminPanel',
         'action'        =>  'createOrganization'],

        ['url'           =>  'admin/organization',
         'controller'    =>  'AdminPanel',
         'action'        =>  'listOrganization'],

        ['url'           =>  'admin/organization/new',
         'controller'    =>  'AdminPanel',
         'action'        =>  'newOrganization'],

        ['url'           =>  'admin/agency/:agency_id/agent/:agent_id',
         'controller'    =>  'Agent',
         'action'        =>  'assignAgency'],

        /*
        ['url'           =>  'admin/unassign/agent/:agent_id',
         'controller'    =>  'Agent',
         'action'        =>  'unassignAgency']*/
         
    ],
    'fallback' => [
        '404' => 'error/404.html'
    ]
];

?>
