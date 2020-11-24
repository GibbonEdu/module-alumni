<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

namespace Gibbon\Module\Alumni;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Alumni Gateway
 *
 * @version 1.0.00
 * @since   1.0.00
 */
class AlumniGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'alumniAlumnus';
    private static $primaryKey = 'alumniAlumnusID';

    private static $searchableColumns = ['preferredName', 'surname', 'username', 'email' ];
    
    /**
     * @param QueryCriteria $criteria
     * @return DataSet
     */
    public function queryAlumniAlumnusByGraduationYear(QueryCriteria $criteria, $graduatinYear = '')
    {
        $query = $this
            ->newQuery()
            ->from($this->getTableName())
            ->cols([
                'alumniAlumnusID',
                'title',
                'surname',
                'firstName',
                'officialName',
                'maidenName',
                'gender',
                'username',
                'dob',
                'email',
                'address1Country',
                'profession',
                'employer',
                'jobTitle',
                'graduatingYear',
                'formerRole',
                'gibbonPersonID',
                'timestamp'
            ]);

        if (!empty($graduatinYear)) {
            $query->where('graduatingYear = :graduatingYear')
                ->bindValue('graduatingYear', $graduatinYear);
        }       

        return $this->runQuery($query, $criteria);
    }
}
