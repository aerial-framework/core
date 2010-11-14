package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CategoryVO;
	import org.aerial.config;

	public class CategoryService extends AbstractService
	{
		public function CategoryService()
		{
			super("CategoryService", IConfig(new Config()).AMF_GATEWAY, CategoryVO);
		}
	}
}