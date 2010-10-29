package org.aerial.rpc
{
	import flash.net.registerClassAlias;
    import flash.utils.IDataInput;
    import flash.utils.IDataOutput;
    import flash.utils.IExternalizable;
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;

    import org.aerial.type.AerialUndefined;


    public class AbstractVO implements IExternalizable
	{
		private var getPrivateProperty:Function;

        private var _nulled:Array = [];
		
		public function AbstractVO(aliasName:String, getProp:Function)
		{
			getPrivateProperty = getProp;
			
			var voType:String = getQualifiedClassName(this).replace("::",".");
			var voClass:Class = getDefinitionByName(voType) as Class;
			
			registerClassAlias(aliasName, voClass);
            _nulled = [];
		}
		
		public function isUndefined(property:String):Boolean
		{
			try{
				var isUndef:Boolean = (getPrivateProperty("_" + property) === undefined);
			}catch(e:ReferenceError){
				throw e;
			}
			return isUndef;
		}

        public function setNull(property:String):void
        {
            if(!this.hasOwnProperty(property))
            {
                var voType:String = getQualifiedClassName(this).replace("::",".");
			    var voClass:Class = getDefinitionByName(voType) as Class;
                throw new ArgumentError("No such property [" + property + "] in " + voClass);
            }

            _nulled.push(property);
        }

        public function clean(value:*):*
        {
            var defaultValue:* = AerialUndefined;
            //return value == defaultValue ? undefined : value;

            trace(defaultValue + " > " + value + " > " + (defaultValue == value));
            return (defaultValue == value) ? undefined : value;
        }
		
		public function readExternal(input:IDataInput):void
		{	
			//We shouldn't have to set this as long as we don't return an IExternalizable from AMFPHP.
			//Otherwise, we'll need to loop through the input and set each property.
		}
		
		public function writeExternal(output:IDataOutput):void
		{
			//To preserve the "undefined" values, we'll just set the private vars.
			var voOutput:Object = new Object();
			
			var descr:XML = describeType(this);
			var props:XMLList = descr..accessor + descr..variable;
			
			for each(var prop:XML in props)
			{ 
				var propName:String = prop.@name.toString();
				try{
					var propValue:* = getPrivateProperty("_" + propName);
				}catch(e:Error){
					throw e;
				}
				voOutput[propName] = propValue;				
			}

            if(_nulled.length > 0)
                voOutput["_nulled"] = _nulled;
            
			output.writeObject(voOutput);
		}
	}

}