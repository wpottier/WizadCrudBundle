<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Model;

use Doctrine\Common\Annotations\Reader;

class PaginatedFilterModel extends FilterModel
{
    private $itemPerPage;

    private $page;

    private $total;

    /**
     * @param Reader $reader
     * @param        $itemPerPage
     */
    public function __construct(Reader $reader, $itemPerPage)
    {
        parent::__construct($reader);

        $this->itemPerPage = $itemPerPage;
        $this->page        = 1;
    }

    /**
     * @param $itemPerPage
     *
     * @return $this
     * @throws \UnexpectedValueException
     */
    public function setItemPerPage($itemPerPage)
    {
        if ($itemPerPage === null) {
            return $this;
        }

        if ($itemPerPage === 0) {
            throw new \UnexpectedValueException('You can not set 0 as a value for the number of item per page.');
        }

        $this->itemPerPage = $itemPerPage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getItemPerPage()
    {
        return $this->itemPerPage;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        if ($page !== null) {
            $this->page = $page;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        if ($this->page == null) {
            return 1;
        }

        return $this->page;
    }

    /**
     * @param $total
     *
     * @return $this
     */
    public function setTotal($total)
    {
        if ($total !== null) {
            $this->total = $total;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function urlizePage()
    {
        return $this->page;
    }

    /**
     * @return mixed
     */
    public function urlizeItemPerPage()
    {
        return $this->itemPerPage;
    }

}