/**
 * Created by IntelliJ IDEA.
 * User: danny
 * Date: 2011/01/20
 * Time: 1:38 AM
 * To change this template use File | Settings | File Templates.
 */
package org.aerial.system
{
    public class DoctrineQuery
    {
        private var _properties:Array;

        public function DoctrineQuery()
        {
            _properties = [];
        }

        private function setProperty(functionName:String, data:*):void
        {
            _properties.push({key:functionName, value:data});
        }

        public function get properties():Array
        {
            return _properties;
        }

        /**
         * Resets the query to the state just after it has been instantiated..
         * 
         * @return
         */
		public function reset():DoctrineQuery
		{
            _properties = [];
            return this;
		}

        /**
         * Resets all the sql parts.
         *
         * @return
         */
		public function clear():DoctrineQuery
		{
            _properties = [];
            return this;
		}

        /**
         * addPendingJoinCondition
         *
         * @param componentAlias
         * @param joinCondition
         * @return
         */
		public function addPendingJoinCondition(componentAlias:String, joinCondition:String):DoctrineQuery
		{
            setProperty("addPendingJoinCondition", [componentAlias, joinCondition]);
            return this;
		}

        /**
         * Adjust the processed param index for "foo.bar IN ?" support
         *
         * @param index
         * @return
         */
		public function adjustProcessedParam(index:Number):DoctrineQuery
		{
            setProperty("adjustProcessedParam", [index]);
            return this;
		}

        /**
         * setOption
         *
         * @param name
         * @param value
         * @return
         */
		public function setOption(name:String, value:String):DoctrineQuery
		{
            setProperty("setOption", [name, value]);
            return this;
		}

        /**
         * setParams
         *
         * @param params
         * @return
         */
		public function setParams(params:Array):DoctrineQuery
		{
            setProperty("setParams", [params]);
            return this;
		}

        /**
         * Adds fields or aliased functions.
         *
         * @param select
         * @return
         */
		public function addSelect(select:String):DoctrineQuery
		{
            setProperty("addSelect", [select]);
            return this;
		}

        /**
         * addSqlTableAlias adds an SQL table alias and associates it a component alias.
         * 
         * @param sqlTableAlias
         * @param componentAlias
         * @param tableAlias
         * @return
         */
		public function addSqlTableAlias(sqlTableAlias:*, componentAlias:String, tableAlias:String):DoctrineQuery
		{
            setProperty("addSqlTableAlias", [sqlTableAlias, componentAlias, tableAlias]);
            return this;
		}

        /**
         * addFrom adds fields to the FROM part of the query.
         *
         * @param from
         * @return
         */
		public function addFrom(from:String):DoctrineQuery
		{
            setProperty("addFrom", [from]);
            return this;
		}

        /**
         * Alias for andWhere()..
         *
         * @see andWhere()
         * @param where
         * @param params
         * @return
         */
		public function addWhere(where:String, params:*):DoctrineQuery
		{
            setProperty("addWhere", [where, params]);
            return this;
		}

        /**
         * Adds conditions to the WHERE part of the query. 
         *
         * @param where
         * @param params
         * @return
         */
		public function andWhere(where:String, params:*):DoctrineQuery
		{
            setProperty("andWhere", [where, params]);
            return this;
		}

        /**
         * Adds conditions to the WHERE part of the query: query.orWhere('u.role = ?', 'admin');
         *
         * @param where
         * @param params
         * @return
         */
		public function orWhere(where:String, params:*):DoctrineQuery
		{
            setProperty("orWhere", [where, params]);
            return this;
		}

        /**
         * Adds IN condition to the query WHERE part.
         *
         * @param expr
         * @param params
         * @param not
         * @return
         */
		public function whereIn(expr:String, params:*, not:Boolean):DoctrineQuery
		{
            setProperty("whereIn", [expr, params, not]);
            return this;
		}

        /**
         * Adds IN condition to the query WHERE part: query.whereIn('u.id', [10, 23, 44]);
         *
         * @param expr
         * @param params
         * @param not
         * @return
         */
		public function andWhereIn(expr:String, params:*, not:Boolean):DoctrineQuery
		{
            setProperty("andWhereIn", [expr, params, not]);
            return this;
		}

        /**
         * Adds IN condition to the query WHERE part, appending it with an OR operator.
         * query.orWhereIn('u.id', [10, 23]).orWhereIn('u.id', 44);
         * will select all record with id equal to 10, 23 or 44
         *
         * @param expr
         * @param params
         * @param not
         * @return
         */
		public function orWhereIn(expr:String, params:*, not:Boolean):DoctrineQuery
		{
            setProperty("orWhereIn", [expr, params, not]);
            return this;
		}

        /**
         * Adds NOT IN condition to the query WHERE part. query.whereNotIn('u.id', [10, 20]);
         * will exclude users with id 10 and 20 from the select
         *
         * @param expr
         * @param params
         * @return
         */
		public function whereNotIn(expr:String, params:*):DoctrineQuery
		{
            setProperty("whereNotIn", [expr, params]);
            return this;
		}

        /**
         * Adds NOT IN condition to the query WHERE part
         *
         * @see whereNotIn()
         * @param expr
         * @param params
         * @return
         */
		public function andWhereNotIn(expr:String, params:*):DoctrineQuery
		{
            setProperty("andWhereNotIn", [expr, params]);
            return this;
		}

        /**
         * Adds NOT IN condition to the query WHERE part
         *
         * @param expr
         * @param params
         * @return
         */
		public function orWhereNotIn(expr:String, params:*):DoctrineQuery
		{
            setProperty("orWhereNotIn", [expr, params]);
            return this;
		}

        /**
         * Adds fields to the GROUP BY part of the query. query.groupBy('u.id');
         *
         * @param groupby
         * @return
         */
		public function addGroupBy(groupby:String):DoctrineQuery
		{
            setProperty("addGroupBy", [groupby]);
            return this;
		}

        /**
         * Adds conditions to the HAVING part of the query.
         * This methods add HAVING clauses. These clauses are used to narrow the results by operating
         * on aggregated values. query.having('num_phonenumbers > ?', 1);
         *
         * @param having
         * @param params
         * @return
         */
		public function addHaving(having:String, params:*):DoctrineQuery
		{
            setProperty("addHaving", [having, params]);
            return this;
		}

        /**
         * addOrderBy adds fields to the ORDER BY part of the query
         *
         * @param orderby
         * @return
         */
		public function addOrderBy(orderby:String):DoctrineQuery
		{
            setProperty("addOrderBy", [orderby]);
            return this;
		}

        /**
         * select sets the SELECT part of the query
         *
         * @param select
         * @return
         */
		public function select(select:String):DoctrineQuery
		{
            setProperty("select", [select]);
            return this;
		}

        /**
         * distinct Makes the query SELECT DISTINCT. query.distinct();
         *
         * @param flag
         * @return
         */
		public function distinct(flag:Boolean):DoctrineQuery
		{
            setProperty("distinct", [flag]);
            return this;
		}

        /**
         * forUpdate Makes the query SELECT FOR UPDATE
         *
         * @param flag
         * @return
         */
		public function forUpdate(flag:Boolean):DoctrineQuery
		{
            setProperty("forUpdate", [flag]);
            return this;
		}

        /**
         * delete sets the query type to DELETE
         * (function should be named "delete" but "delete" is a reserved word in ActionScript 3.0)
         *
         * @param from
         * @return
         */
		public function deleteFrom(from:String):DoctrineQuery
		{
            setProperty("deleteFrom", [from]);
            return this;
		}

        /**
         * update sets the UPDATE part of the query
         *
         * @param from
         * @param update
         * @return
         */
		public function update(from:*, update:String):DoctrineQuery
		{
            setProperty("update", [from, update]);
            return this;
		}

        /**
         * set sets the SET part of the query
         * (function should be named "set" but "set" is a reserved word in ActionScript 3.0)
         *
         * @param key
         * @param value
         * @param params
         * @param update
         * @return
         */
		public function setSet(key:*, value:*, params:*, update:String):DoctrineQuery
		{
            setProperty("setSet", [key, value, params, update]);
            return this;
		}

        /**
         * from sets the FROM part of the query: query.from('User u');
         *
         * @param from
         * @return
         */
		public function from(from:String):DoctrineQuery
		{
            setProperty("from", [from]);
            return this;
		}

        /**
         * innerJoin appends an INNER JOIN to the FROM part of the query
         *
         * @param join
         * @param params
         * @return
         */
		public function innerJoin(join:String, params:*):DoctrineQuery
		{
            setProperty("innerJoin", [join, params]);
            return this;
		}

		public function leftJoin(join:String, params:*):DoctrineQuery
		{
            setProperty("leftJoin", [join, params]);
            return this;
		}

        /**
         * groupBy sets the GROUP BY part of the query
         *
         * @param groupby
         * @return
         */
		public function groupBy(groupby:String):DoctrineQuery
		{
            setProperty("groupBy", [groupby]);
            return this;
		}

        /**
         * where sets the WHERE part of the query
         *
         * @param where
         * @param params
         * @param join
         * @return
         */
		public function where(where:*, params:*, join:String):DoctrineQuery
		{
            setProperty("where", [where, params, join]);
            return this;
		}

        /**
         * having sets the HAVING part of the query
         *
         * @param having
         * @param params
         * @return
         */
		public function having(having:String, params:*):DoctrineQuery
		{
            setProperty("having", [having, params]);
            return this;
		}

        /**
         * Sets the ORDER BY part of the query.
         * query.orderBy('u.name');
         * query.orderBy('u.birthDate DESC');
         *
         * @param orderby
         * @return
         */
		public function orderBy(orderby:String):DoctrineQuery
		{
            setProperty("orderBy", [orderby]);
            return this;
		}

        /**
         * limit sets the Query query limit
         *
         * @param limit
         * @return
         */
		public function limit(limit:int):DoctrineQuery
		{
            setProperty("limit", [limit]);
            return this;
		}

        /**
         * offset sets the Query query offset
         *
         * @param offset
         * @return
         */
		public function offset(offset:int):DoctrineQuery
		{
            setProperty("offset", [offset]);
            return this;
		}

        /**
         * setHydrationMode
         *
         * @param hydrationMode
         * @return
         */
		public function setHydrationMode(hydrationMode:*):DoctrineQuery
		{
            setProperty("setHydrationMode", [hydrationMode]);
            return this;
		}

        /**
         * Return a sample PHP representation of this object
         *
         * @return
         */
        public function toString():String
        {
            var asString:String = "$query = Doctrine_Query::create()";
            for each(var property:Object in properties)
                asString += "\n\t->" + property.key + "(" + getParametersAsString(property.value as Array) + ")";

            asString += ";";

            return asString;
        }

        private function getParametersAsString(values:Array):String
        {
            var params:Array = [];
            for each(var value:* in values)
            {
                if(value is String)
                    params.push("'" + value + "'");
                else
                    params.push(value);
            }

            return params.join(", ");
        }
    }
}
