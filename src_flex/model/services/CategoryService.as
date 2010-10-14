package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.CategoryVO
	import com.forum.config.Config;

	public class CategoryService extends AbstractService
	{
		public function CategoryService()
		{
			super("CategoryService", IConfig(new Config()).AMF_GATEWAY, CategoryVO);
		}
	}
}