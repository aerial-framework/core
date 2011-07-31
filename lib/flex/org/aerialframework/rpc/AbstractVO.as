package org.aerialframework.rpc
{
    import flash.utils.IDataInput;
    import flash.utils.IDataOutput;
    import flash.utils.IExternalizable;
    import flash.utils.describeType;
    import flash.utils.getDefinitionByName;
    import flash.utils.getQualifiedClassName;

    public class AbstractVO implements IExternalizable
    {
        private var getPrivateProperty:Function;
        private var setPrivateProperty:Function;

        public function AbstractVO(getProp:Function, setProp:Function)
        {
            getPrivateProperty = getProp;
            setPrivateProperty = setProp;
        }

        /**
         * Checks whether a property is undefined
         *
         * @param property
         * @return Boolean
         */
        public function isUndefined(property:String):Boolean
        {
            try
            {
                var isUndef:Boolean = (getPrivateProperty("_" + property) === undefined);
            }
            catch(e:ReferenceError)
            {
                throw e;
            }
            return isUndef;
        }

        /**
         * Checks whether a property is null
         *
         * @param property
         * @return Boolean
         */
        public function isNull(property:String):Boolean
        {
            try
            {
                var _isNull:Boolean = (getPrivateProperty("_" + property) === null);
            }
            catch(e:ReferenceError)
            {
                throw e;
            }
            return _isNull;
        }

        /**
         * Nullifies a particular property
         *
         * @param property
         */
        public function setNull(property:String):void
        {
            if(!this.hasOwnProperty(property))
            {
                var voType:String = getQualifiedClassName(this).replace("::", ".");
                var voClass:Class = getDefinitionByName(voType) as Class;
                throw new ArgumentError("No such property [" + property + "] in " + voClass);
            }

            setPrivateProperty("_" + property, null);
        }

        /**
         * Unset a particular property
         *
         * @param property
         */
        public function unset(property:String):void
        {
            if(!this.hasOwnProperty(property))
            {
                var voType:String = getQualifiedClassName(this).replace("::", ".");
                var voClass:Class = getDefinitionByName(voType) as Class;
                throw new ArgumentError("No such property [" + property + "] in " + voClass);
            }

            setPrivateProperty("_" + property, undefined);
        }

        /**
         * Implemented to support the IExternalizable interface
         *
         * @param input
         */
        public function readExternal(input:IDataInput):void
        {
            //We shouldn't have to set this as long as we don't return an IExternalizable from AMFPHP.
            //Otherwise, we'll need to loop through the input and set each property.
        }

        /**
         * Implemented to suppoprt the IExternalizable interface
         *
         * @param output
         */
        public function writeExternal(output:IDataOutput):void
        {
            //To preserve the "undefined" values, we'll just set the private vars.
            var voOutput:Object = new Object();

            var descr:XML = describeType(this);
            var props:XMLList = descr..accessor + descr..variable;

            for each(var prop:XML in props)
            {
                var propName:String = prop.@name.toString();
                try
                {
                    var propValue:* = getPrivateProperty("_" + propName);
                }
                catch(e:Error)
                {
                    throw e;
                }
                voOutput[propName] = propValue;
            }

            output.writeObject(voOutput);
        }
    }

}