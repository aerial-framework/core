package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.CategoryVO;
	import org.aerial.bootstrap.Aerial;

	public class CategoryService extends AbstractService
	{
		public function CategoryService()
		{
			super("CategoryService", Aerial.SERVER_URL, CategoryVO);
		}
	}
}