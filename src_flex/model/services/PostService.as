package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.PostVO
	import com.forum.config.Config;

	public class PostService extends AbstractService
	{
		public function PostService()
		{
			super("PostService", IConfig(new Config()).AMF_GATEWAY, PostVO);
		}
	}
}