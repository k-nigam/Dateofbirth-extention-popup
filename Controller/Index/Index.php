<?php

namespace ArtM\Dob\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Index extends Action
{
    protected $customerSession;
    protected $customerRepository;

    public function __construct
    (
        Context $context, 
        PageFactory $resultPageFactory,
        RequestInterface $request,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->request = $request;   
        $this->resultPageFactory = $resultPageFactory; 
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;    
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $result = $this->_resultJsonFactory->create();
        
        $formData = $this->request->getPost();

        $year = 1;
        
        if (strlen($formData['year']) == '4'){
            $year = 4;
        }
       

        if($formData['dob_customer'] != "" && $formData['day'] != ""  && $formData['month'] != "" && $formData['year']  != "" && $year == '4'){
            
            $dob = $formData['dob_customer'];
    
            $save = $this->saveCustomerDob($dob);
    
            if($save){
                $result->setData(['result' => "True"]);
            }else{
                $result->setData(['result' => 'False']);
            }

            return $result;
        }
        return $result->setData(['result' => 'False']);

    }

    public function getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerId();
        }
        return false;
    }

    public function saveCustomerDob($dob)
    {
       $customerId = $this->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setDob($dob);
            $this->customerRepository->save($customer);
            return true;
        }
        return false;
    }
}
